<?php

namespace TddWizard\Fixtures\Checkout;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Quote\Model\GuestCart\GuestCartManagement;
use Magento\Quote\Model\GuestCart\GuestCartRepository;
use \Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;


class CartBuilder
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var DataObject[][] Array in the form [sku => [buyRequest]] (multiple requests per sku are possible)
     */
    private $addToCartRequests;

    /**
     * @var GuestCartManagement
     */
    private $quoteManagement;

    /**
     * @var GuestCartRepository
     */
    private $guestCartRepository;

    public function __construct(ProductRepositoryInterface $productRepository, Cart $cart, GuestCartManagement $quoteManagement, GuestCartRepository $guestCartRepository)
    {
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->addToCartRequests = [];
        $this->quoteManagement = $quoteManagement;
        $this->guestCartRepository = $guestCartRepository;

    }

    public static function forCurrentSession(ObjectManagerInterface $objectManager = null)
    {
        if ($objectManager === null) {
            $objectManager = Bootstrap::getObjectManager();
        }
        return new static(
            $objectManager->create(ProductRepositoryInterface::class),
            $objectManager->create(Cart::class),
            $objectManager->create(GuestCartManagement::class),
            $objectManager->create(GuestCartRepository::class)
        );
    }

    public function withSimpleProduct($sku, $qty = 1) : CartBuilder
    {
        $result = clone $this;
        $result->addToCartRequests[$sku][] = new DataObject(['qty' => $qty]);
        return $result;
    }

    public function build() : Cart
    {
        foreach ($this->addToCartRequests as $sku => $requests) {
            /** @var $product \Magento\Catalog\Model\Product */
            $product = $this->productRepository->get($sku);
            foreach ($requests as $requestInfo) {
                $this->cart->addProduct($product, $requestInfo);
            }
        }
        $this->cart->save();
        return $this->cart;
    }

    public function buildGuest() : String
    {
        $maskedId = $this->quoteManagement->createEmptyCart();
        $this->cart = $this->guestCartRepository->get($maskedId);
        foreach ($this->addToCartRequests as $sku => $requests) {
            /** @var $product \Magento\Catalog\Model\Product */
            $product = $this->productRepository->get($sku);
            foreach ($requests as $requestInfo) {
                $this->cart->addProduct($product, $requestInfo);
            }
        }
        $this->cart->save();
        return $maskedId;
    }
}
