<?php

namespace Experius\Ponumber\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class InstallData implements InstallDataInterface
{

    protected $quoteSetupFactory;
    protected $salesSetupFactory;
    protected $customerSetupFactory;

    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        $options = ['type' => 'varchar','length' => 255, 'visible' => false, 'required' => false,'grid' => true];
        
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $quoteSetup->addAttribute('quote', 'experius_po_number', $options);

        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $salesSetup->addAttribute('order', 'experius_po_number', $options);

        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $salesSetup->addAttribute('invoice', 'experius_po_number', $options);        
        
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);                        
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();        
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        
        $customerSetup->addAttribute('customer', 'experius_po_number_required', [            
            'type' => 'int',
            'label' => 'Po number in checkout required',
            'input' => 'boolean',
            'backend' => \Magento\Customer\Model\Attribute\Backend\Data\Boolean::class,
            'position' => 8,
            'required' => true,
            'user_defined' => true,
            'default' => true,
            'adminhtml_only' => true,
            'sort_order' => 90,                
            'system' => 0
                        
        ]);
        
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'experius_po_number_required')
            ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer']]);
            
        $attribute->save();                     
        
    }
}


