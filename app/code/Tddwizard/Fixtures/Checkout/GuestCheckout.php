<?php

namespace TddWizard\Fixtures\Checkout;

use Magento\Checkout\Model\Cart;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Quote\Model\GuestCart\GuestBillingAddressManagement;
use Magento\Quote\Model\GuestCart\GuestCartRepository;
use Magento\Quote\Model\GuestCart\GuestCartManagement;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use \Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\GuestCart\GuestShippingMethodManagement;

class GuestCheckout
{
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;
    /**
     * @var GuestBillingAddressManagement
     */
    private $addressRepository;
    /**
     * @var GuestCartRepository
     */
    private $quoteRepository;
    /**
     * @var GuestCartManagement
     */
    private $quoteManagement;
    /**
     * @var PaymentConfig
     */
    private $paymentConfig;
    /**
     * @var Quote
     */
    private $cart;
    /**
     * @var int|null
     */
    private $customerShippingAddressId;
    /**
     * @var int|null
     */
    private $customerBillingAddressId;
    /**
     * @var string|null
     */
    private $shippingMethodCode;
    /**
     * @var string|null
     */
    private $paymentMethodCode;

    /**
     * @var String
     */
    private $maskedId;

    /**
     * @var GuestShippingMethodManagement
     */
    private $guestShippingMethodManagement;

    public function __construct(
        GuestBillingAddressManagement $addressRepository,
        GuestCartRepository $quoteRepository,
        GuestCartManagement $quoteManagement,
        PaymentConfig $paymentConfig,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        String $maskedId,
        GuestShippingMethodManagement $guestShippingMethodManagement,
        $customerShippingAddressId = null,
        $customerBillingAddressId = null,
        $shippingMethodCode = null,
        $paymentMethodCode = null
    ) {

        $this->addressRepository = $addressRepository;
        $this->quoteRepository = $quoteRepository;
        $this->quoteManagement = $quoteManagement;
        $this->paymentConfig = $paymentConfig;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->maskedId = $maskedId;
        $this->guestShippingMethodManagement = $guestShippingMethodManagement;
        $this->cart = $this->quoteRepository->get($maskedId);
        $this->customerShippingAddressId = $customerShippingAddressId;
        $this->customerBillingAddressId = $customerBillingAddressId;
        $this->shippingMethodCode = $shippingMethodCode;
        $this->paymentMethodCode = $paymentMethodCode;
    }

    public static function fromCart(String $maskedId, ObjectManagerInterface $objectManager = null) : GuestCheckout
    {
        if ($objectManager === null) {
            $objectManager = Bootstrap::getObjectManager();
        }
        return new static(
            $objectManager->create(GuestBillingAddressManagement::class),
            $objectManager->create(GuestCartRepository::class),
            $objectManager->create(GuestCartManagement::class),
            $objectManager->create(PaymentConfig::class),
            $objectManager->create(QuoteIdMaskFactory::class),
            $maskedId,
            $objectManager->create(GuestShippingMethodManagement::class)
        );
    }

    public function withCustomerBillingAddressId(int $addressId) : GuestCheckout
    {
        $checkout = clone $this;
        $checkout->customerBillingAddressId = $addressId;
        return $checkout;
    }

    public function withCustomerShippingAddressId(int $addressId) : GuestCheckout
    {
        $checkout = clone $this;
        $checkout->customerShippingAddressId = $addressId;
        return $checkout;
    }

    public function withShippingMethodCode(string $code) : GuestCheckout
    {
        $checkout = clone $this;
        $checkout->shippingMethodCode = $code;
        return $checkout;
    }

    public function withPaymentMethodCode(string $code) : GuestCheckout
    {
        $checkout = clone $this;
        $checkout->paymentMethodCode = $code;
        return $checkout;
    }

    public function withQuoteBillingAddressId(int $addressId) : GuestCheckout
    {
        $checkout = clone $this;
        $checkout->customerBillingAddressId = $addressId;
        return $checkout;
    }

    public function withQuoteShippingAddressId(int $addressId) : GuestCheckout
    {
        $checkout = clone $this;
        $checkout->customerShippingAddressId = $addressId;
        return $checkout;
    }

    public function withPoNumber(int $number) : GuestCheckout
    {
        $checkout = clone $this;
        $this->cart->setExperiusPoNumber($number);
        $this->cart->save();
        return $checkout;
    }

    /**
     * @return string Payment method code as configured, or try first available method
     */
    private function getPaymentMethodCode() : string
    {
        return $this->paymentMethodCode ?? array_values($this->paymentConfig->getActiveMethods())[0]->getCode();
    }

    public function placeOrder() : int
    {
        $this->saveBilling();
        $this->saveShipping();
        $this->savePayment();
        $this->cart->setCustomerEmail(sha1(uniqid('', true)) . '@example.com');
        $this->cart->setCustomerIsGuest(1);
        $this->cart->save();
        $orderId = $this->quoteManagement->placeOrder($this->maskedId);
        return $orderId;
    }

    private function saveBilling()
    {
        $billingAddress = $this->cart->getBillingAddress();
        $billingAddress->save();
    }

    private function saveShipping()
    {
        $this->guestShippingMethodManagement->set($this->maskedId, 'flatrate', 'flatrate');
    }

    private function savePayment()
    {
        $payment = $this->cart->getPayment();
        $payment->setMethod($this->getPaymentMethodCode());
        $payment->save();
    }
}
