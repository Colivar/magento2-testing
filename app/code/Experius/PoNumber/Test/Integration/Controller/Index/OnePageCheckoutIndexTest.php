<?php
/**
 * Created by PhpStorm.
 * User: carl
 * Date: 9/29/17
 * Time: 12:16 PM
 */

use Magento\TestFramework\Request;
use Magento\TestFramework\TestCase\AbstractController as ControllerTestCase;
use Zend\Stdlib\ParametersInterface;
use TddWizard\Fixtures\Customer\CustomerBuilder;
use TddWizard\Fixtures\Customer\CustomerFixture;
use TddWizard\Fixtures\Customer\AddressBuilder;
use TddWizard\Fixtures\Checkout\CartBuilder;
use TddWizard\Fixtures\Checkout\CustomerCheckout;
use TddWizard\Fixtures\Catalog\ProductBuilder;
use TddWizard\Fixtures\Catalog\ProductFixture;
use TddWizard\Fixtures\Quote\QuoteAddressBuilder;
use TddWizard\Fixtures\Quote\QuoteShippingAddressBuilder;
use TddWizard\Fixtures\Checkout\GuestCheckout;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Sales\Model\OrderRepository;

class OnePageCheckoutIndexTest extends ControllerTestCase
{
    private $objectManager;

    /**
     * Navigate with product in cart to checkout as guest
     * expecting Po number to be in body and responsecode 200
     */
    public function testGuestIndex()
    {
        $productFixture = new ProductFixture(
            ProductBuilder::aSimpleProduct()
                ->withPrice(10)
                ->withCustomAttributes(
                    [
                        'my_custom_attribute' => 42
                    ]
                )
                ->build()
        );

        CartBuilder::forCurrentSession()
            ->withSimpleProduct(
                $productFixture->getSku()
            )
            ->build();

        /** @var \Magento\TestFramework\Request $request */
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);

        $this->dispatch('/checkout');

        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());
        //$this->assertContains('"experius-ponumber-form-container":{"component":"Experius_Ponumber\/js\/view\/form\/ponumber","provider":"checkoutProvider","config":{"template":"Experius_Ponumber\/form"},"children":{"experius-ponumber-form-fieldset":{"component":"uiComponent","displayArea":"custom-checkout-form-fields","children":{"experius_po_number":{"component":"Experius_Ponumber\/js\/view\/form\/element\/ponumber","config":{"customerScope":"experiusPonumberForm","template":"ui\/form\/field","elementTmpl":"ui\/form\/element\/input","tooltip":{"description":"Voer uw inkoopnummer in"}},"provider":"checkoutProvider","dataScope":"experiusPonumberForm.experius_po_number","label":"Inkoop ordernummer","sortOrder":"1","validation":{"required-entry":false}}}}}}}}', $this->getResponse()->getBody());
    }

    /**
     * Navigate with product in cart to checkout as customer
     * expecting Po number to in body and responsecode 200
     */
    public function testCustomerIndex()
    {
        $productFixture = new ProductFixture(
            ProductBuilder::aSimpleProduct()
                ->withPrice(10)
                ->withCustomAttributes(
                    [
                        'my_custom_attribute' => 42
                    ]
                )
                ->build()
        );

        CartBuilder::forCurrentSession()
            ->withSimpleProduct(
                $productFixture->getSku()
            )
            ->build();
        $customerFixture = new CustomerFixture(CustomerBuilder::aCustomer()
            ->withAddresses(
                AddressBuilder::anAddress()->asDefaultBilling()->asDefaultShipping()
            )->withCustomAttributes(array('experius_po_number_required'=>1))
            ->build());

        $customerFixture->login();

        /** @var \Magento\TestFramework\Request $request */
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);

        $this->dispatch('/checkout');

        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());
        //$this->assertContains('"experius-ponumber-form-container":{"component":"Experius_Ponumber\/js\/view\/form\/ponumber","provider":"checkoutProvider","config":{"template":"Experius_Ponumber\/form"},"children":{"experius-ponumber-form-fieldset":{"component":"uiComponent","displayArea":"custom-checkout-form-fields","children":{"experius_po_number":{"component":"Experius_Ponumber\/js\/view\/form\/element\/ponumber","config":{"customerScope":"experiusPonumberForm","template":"ui\/form\/field","elementTmpl":"ui\/form\/element\/input","tooltip":{"description":"Voer uw inkoopnummer in"}},"provider":"checkoutProvider","dataScope":"experiusPonumberForm.experius_po_number","label":"Inkoop ordernummer","sortOrder":"1","validation":{"required-entry":false}}}}}}}}', $this->getResponse()->getBody());
    }

    /**
     * Place an order as guest
     * expecting order to contain experius_po_number
     */
    public function testGuestSuccesCheckout()
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productFixture = new ProductFixture(
            ProductBuilder::aSimpleProduct()
                ->withPrice(10)
                ->withCustomAttributes(
                    [
                        'my_custom_attribute' => 42
                    ]
                )
                ->build()
        );
        $this->dispatch('/checkout');
        $maskedId = CartBuilder::forCurrentSession()
            ->withSimpleProduct(
                $productFixture->getSku()
            )
            ->buildGuest();

        $checkout = GuestCheckout::fromCart(
        $maskedId)
            ->withQuoteBillingAddressId(QuoteAddressBuilder::anAddress()->build($maskedId))
            ->withQuoteShippingAddressId(QuoteShippingAddressBuilder::anAddress()->build($maskedId))
            ->withPoNumber(12);

        $orderId = $checkout->placeOrder();
        $objectRepo = $this->objectManager->create(OrderRepository::class);
        $order = $objectRepo->get($orderId);
        try {
            $po_number = $order->getData('experius_po_number');
        } catch (Exception $e) {
            $po_number = '';
        }
        $this->assertContains('@example.com', $order->getBillingAddress()->getEmail());
        $this->assertContains('12', $po_number);
        $this->assertContains('pending', $order->getStatus());

    }

    /**
     * Place an order as customer
     * Expecting order to contain experius_po_number
     */
    public function testCustomerSuccesCheckout()
    {
        $productFixture = new ProductFixture(
            ProductBuilder::aSimpleProduct()
                ->withPrice(10)
                ->withCustomAttributes(
                    [
                        'my_custom_attribute' => 42
                    ]
                )
                ->build()
        );

        $customerFixture = new CustomerFixture(CustomerBuilder::aCustomer()
            ->withAddresses(
                AddressBuilder::anAddress()->asDefaultBilling()->asDefaultShipping()
            )->withCustomAttributes(array('experius_po_number_required'=>1))
            ->build());

        $customerFixture->login();
        $this->dispatch('/checkout');

        $checkout = CustomerCheckout::fromCart(
            CartBuilder::forCurrentSession()
                ->withSimpleProduct(
                    $productFixture->getSku()
                )
                ->build()
        )->withPoNumber(12);

        $order = $checkout->placeOrder();
        $this->assertContains('@example.com', $order->getBillingAddress()->getEmail());
        $this->assertContains('pending', $order->getStatus());
        $this->assertContains('12',$order->getData('experius_po_number'));
    }



//    /**
//     * @magentoAppIsolation enabled
//     */
//    public function testSaveBillingMethod()
//    {
//        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//
//        $quoteIdMaskFactory = $this->objectManager->create(QuoteIdMaskFactory::class);
//        $class = \Magento\TestFramework\App\State::class;
//        $appAreaState = $this->objectManager->get($class);
//        $productFixture = new ProductFixture(
//            ProductBuilder::aSimpleProduct()
//                ->withPrice(10)
//                ->withCustomAttributes(
//                    [
//                        'my_custom_attribute' => 42
//                    ]
//                )
//                ->build()
//        );
//
//        $cart = CartBuilder::forCurrentSession()
//            ->withSimpleProduct(
//                $productFixture->getSku()
//            )
//            ->build();
//
//        $cartId = $cart->getQuote()->getId();
//
//        $this->dispatch('/checkout');
//        $quoteIdMask = $quoteIdMaskFactory->create()->load($cartId, 'quote_id');
//        $maskedId = $quoteIdMask->getMaskedId();
//        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//
//        $params = array("addressInformation" =>
//            array("billing_address"=> array(
//                "city"=>"Utrecht",
//                "company"=>"",
//                "countryId"=>"NL",
//                "firstname"=>"carl",
//                "lastname"=>"maassen",
//                "postcode"=>"3511 MJ",
//                "region"=>"",
//                "regionId"=>"0",
//                "saveInAddressBook"=>null),
//            "shipping_address"=>array(
//                "city"=>"Utrecht",
//                "company"=>"",
//                "countryId"=>"NL",
//                "firstname"=>"carl",
//                "lastname"=>"maassen",
//                "postcode"=>"3511 MJ",
//                "region"=>"",
//                "regionId"=>"0",
//                "saveInAddressBook"=>null),
//                    ));
//        $appAreaState->setAreaCode('webapi_rest');
//        $this->getRequest()->setMethod('POST')->setPostValue($params);
//        $this->dispatch('rest/default/V1/guest-carts/'.$maskedId.'/shipping-information');
//        echo $this->getRequest()->getRequestUri();
//        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());
//        $this->assertContains('payment_methods', $this->getResponse()->getBody());
        //$this->dispatch('/payment-information');
//        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());
//        $this->assertContains('/onepage/success/', $this->getResponse()->getUrlFromPath());
//    }
}