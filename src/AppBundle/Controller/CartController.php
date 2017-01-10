<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CartItem;
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\Event\CartItemEvent;
use Sylius\Component\Cart\SyliusCartEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CartController.
 */
class CartController extends Controller
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function addAction(Request $request)
    {
        $cart      = $this->getCurrentCart();
        $emptyItem = new CartItem();

        $eventDispatcher = $this->get('event_dispatcher');
        $eventDispatcher->dispatch(SyliusCartEvents::CART_CLEAR_INITIALIZE, new CartEvent($this->getCurrentCart()));

        try {
            $item = $this->getResolver()->resolve($emptyItem, $request);
        } catch (ItemResolvingException $exception) {
            // Write flash message
            $eventDispatcher->dispatch(SyliusCartEvents::ITEM_ADD_ERROR, new FlashEvent($exception->getMessage()));

            return $this->redirectAfterAdd($request);
        }

        $event = new CartItemEvent($cart, $item);
        $event->isFresh(true);

        // Update models
        $eventDispatcher->dispatch(SyliusCartEvents::ITEM_ADD_INITIALIZE, $event);
        $eventDispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($cart));
        $eventDispatcher->dispatch(SyliusCartEvents::CART_SAVE_INITIALIZE, $event);

        return $this->redirectAfterAdd($request);
    }

    /**
     * Redirect to cart summary page.
     *
     * @return RedirectResponse
     */
    protected function redirectToCartSummary()
    {
        return $this->redirect($this->generateUrl($this->getCartSummaryRoute()));
    }

    /**
     * Cart summary page route.
     *
     * @return string
     */
    protected function getCartSummaryRoute()
    {
        return 'sylius_cart_summary';
    }

    /**
     * Get cart item resolver.
     * This service is used to build the new cart item instance.
     *
     * @return ItemResolverInterface
     */
    protected function getResolver()
    {
        return $this->container->get('sylius.cart_resolver');
    }

    /**
     * Redirect to specific URL or to cart.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    private function redirectAfterAdd(Request $request)
    {
        if ($request->query->has('_redirect_to')) {
            return $this->redirect($request->query->get('_redirect_to'));
        }

        return $this->redirectToCartSummary();
    }

    /**
     * Get current cart using the provider service.
     *
     * @return CartInterface
     */
    protected function getCurrentCart()
    {
        return $this
            ->getProvider()
            ->getCart();
    }

    /**
     * Get cart provider.
     *
     * @return CartProviderInterface
     */
    protected function getProvider()
    {
        return $this->container->get('sylius.cart_provider');
    }
}
