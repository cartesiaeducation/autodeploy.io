<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Cart\Model\CartItem as BaseCartItem;
use Sylius\Component\Order\Model\OrderItemInterface;

/**
 * Class CartItem.
 *
 * @ORM\Table(name="app_cart_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartRepository")
 */
class CartItem extends BaseCartItem
{
    /**
     * @var \AppBundle\Entity\Plan
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Plan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="plan_id", referencedColumnName="id")
     * })
     */
    protected $plan;

    /**
     * @return Plan
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * @param Plan $plan
     */
    public function setVariant(Plan $plan)
    {
        $this->plan = $plan;
    }

    /**
     * @param OrderItemInterface $item
     *
     * @return bool
     */
    public function equals(OrderItemInterface $item)
    {
        return $this->plan === $item->getPlan();
    }
}
