<?php

namespace App\Services\Cart;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartSessionStorage
{
    /**
     * The request stack.
     *
     * @var RequestStack
     */
    private $requestStack;

    /**
     * The cart repository.
     *
     * @var CommandeRepository
     */
    private $cartRepository;

    /**
     * @var string
     */
    const CART_KEY_NAME = 'cart_id';

    /**
     * CartSessionStorage constructor.
     *
     * @param RequestStack $requestStack
     * @param CommandeRepository $cartRepository
     */
    public function __construct(RequestStack $requestStack, CommandeRepository $cartRepository)
    {
        $this->requestStack = $requestStack;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Gets the cart in session.
     *
     * @return Commande|null
     */
    public function getCart(): ?Commande
    {
        return $this->cartRepository->findOneBy([
            'id' => $this->getCartId(),
            'status' => Commande::STATUS_CART
        ]);
    }

    /**
     * Sets the cart in session.
     *
     * @param Commande $cart
     */
    public function setCart(Commande $cart): void
    {
        $this->getSession()->set(self::CART_KEY_NAME, $cart->getId());
    }

    /**
     * Returns the cart id.
     *
     * @return int|null
     */
    private function getCartId(): ?int
    {
        return $this->getSession()->get(self::CART_KEY_NAME);
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}