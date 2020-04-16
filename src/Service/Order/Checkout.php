<?php

namespace Service\Order;

use Service\Billing\BillingInterface;
use Service\Billing\Transfer\Card;
use Service\Communication\CommunicationInterface;
use Service\Communication\Sender\Email;
use Service\Discount\DiscountInterface;
use Service\User\Security;
use Service\User\SecurityInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Checkout
{
    /**
     * @var $billing BillingInterface
    */
    protected $billing;
    /**
     * @var $discount DiscountInterface
     */
    protected $discount;
    /**
     * @var $communication CommunicationInterface
     */
    protected $communication;
    /**
     * @var $security SecurityInterface
     */
    protected $security;

    /**
     * Бывает удобным сделать Фасад ответственным за управление жизненным циклом
     * используемой подсистемы.
     */
    public function __construct(SessionInterface $session)
    {
        $this->billing = new Card();
        $this->communication = new Email();
        $this->security = new Security($session);
    }

    public function checkoutProcess(DiscountInterface $discount, array $products): void
    {
        $this->discount = $discount->getDiscount();

        $totalPrice = 0;

        foreach ($products as $product) {
            $totalPrice += $product->getPrice();
        }

        $totalPrice = $totalPrice - $totalPrice / 100 * $this->discount;

        $this->billing->pay($totalPrice);

        $user = $this->security->getUser();
        $this->communication->process($user, 'checkout_template');
    }
}