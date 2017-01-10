<?php

namespace AppBundle\Cart;

use Doctrine\ORM\EntityManager;
use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Cart\Resolver\ItemResolverInterface;
use Sylius\Component\Cart\Resolver\ItemResolvingException;

/**
 * Class ItemResolver.
 */
class ItemResolver implements ItemResolverInterface
{
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param CartItemInterface $item
     * @param Request           $request
     *
     * @return CartItemInterface
     */
    public function resolve(CartItemInterface $item, $request)
    {
        $slug = $request->get('slug');

        // If no product id given, or product not found, we throw exception with nice message.
        if (!$slug || !$plan = $this->getPlanRepository()->findOneByName($slug)) {
            throw new ItemResolvingException('Requested plan was not found');
        }

        $item->setVariant($plan);
        $item->setUnitPrice((int) $plan->getPrice());

        return $item;
    }

    /**
     * @return \AppBundle\Repository\PlanRepository
     */
    private function getPlanRepository()
    {
        return $this->em->getRepository('AppBundle:Plan');
    }
}
