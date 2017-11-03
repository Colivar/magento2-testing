<?php

namespace TddWizard\Fixtures\Quote;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\BillingAddressManagement;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\GuestCart\GuestCartRepository;
use Magento\Quote\Model\ShippingAddressManagement;

/**
 * Builder to be used by fixtures
 */
class QuoteShippingAddressBuilder
{
    /**
     * @var GuestCartRepository
     */
    protected $quoteRepository;
    /**
     * @var AddressInterface
     */
    private $address;
    /**
     * @var BillingAddressManagement
     */
    private $addressRepository;

    public function __construct(ShippingAddressManagement $addressRepository, AddressInterface $address, GuestCartRepository $quoteRepository)
    {
        $this->addressRepository = $addressRepository;
        $this->quoteRepository = $quoteRepository;
        $this->address = $address;
    }

    public function __clone()
    {
        $this->address = clone $this->address;
    }

    public function withCustomAttributes(array $values) : QuoteShippingAddressBuilder
    {
        $builder = clone $this;
        foreach ($values as $code => $value) {
            $builder->address->setCustomAttribute($code, $value);
        }
        return $builder;
    }

    public static function anAddress(ObjectManagerInterface $objectManager = null) : QuoteShippingAddressBuilder
    {
        if ($objectManager === null) {
            $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        }
        /** @var AddressInterface $address */
        $address = $objectManager->create(AddressInterface::class);
        $address
            ->setTelephone('3468676')
            ->setPostcode('75477')
            ->setCountryId('US')
            ->setCity('CityM')
            ->setCompany('CompanyName')
            ->setStreet(['Green str, 67'])
            ->setLastname('Smith')
            ->setFirstname('John')
            ->setRegionId(1);
        return new self($objectManager->create(ShippingAddressManagement::class), $address, $objectManager->create(GuestCartRepository::class));
    }


//    public function asDefaultShipping() : QuoteAddressBuilder
//    {
//        $builder = clone $this;
//        $builder->address->setIsDefaultShipping(true);
//        return $builder;
//    }
//
//    public function asDefaultBilling() : QuoteAddressBuilder
//    {
//        $builder = clone $this;
//        $builder->address->setIsDefaultBilling(true);
//        return $builder;
//    }

    public function build(String $maskedId) : int
    {
        $quote = $this->quoteRepository->get($maskedId);
        $quoteId = $quote->getId();
        return $this->addressRepository->assign($quoteId ,$this->address);
    }

    public function buildWithoutSave() : AddressInterface
    {
        return clone $this->address;
    }

    public function withFirstname($firstname) : QuoteShippingAddressBuilder
    {
        $builder = clone $this;
        $builder->address->setFirstname($firstname);
        return $builder;
    }

    public function withLastname($lastname) : QuoteShippingAddressBuilder
    {
        $builder = clone $this;
        $builder->address->setLastname($lastname);
        return $builder;
    }

    public function withStreet($street) : QuoteShippingAddressBuilder
    {
        $builder = clone $this;
        $builder->address->setStreet((array) $street);
        return $builder;
    }

    public function withCompany($company) : QuoteShippingAddressBuilder
    {
        $builder = clone $this;
        $builder->address->setCompany($company);
        return $builder;
    }

    public function withTelephone($telephone) : QuoteShippingAddressBuilder
    {
        $builder = clone $this;
        $builder->address->setTelephone($telephone);
        return $builder;
    }

    public function withPostcode($postcode) : QuoteShippingAddressBuilder
    {
        $builder = clone $this;
        $builder->address->setPostcode($postcode);
        return $builder;
    }

    public function withCity($city) : QuoteShippingAddressBuilder
    {
        $builder = clone $this;
        $builder->address->setCity($city);
        return $builder;
    }

    public function withCountryId($countryId) : QuoteShippingAddressBuilder
    {
        $builder = clone $this;
        $builder->address->setCountryId($countryId);
        return $builder;
    }

    public function withRegionId($regionId) : QuoteShippingAddressBuilder
    {
        $builder = clone $this;
        $builder->address->setRegionId($regionId);
        return $builder;
    }
}
