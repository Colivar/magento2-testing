<?php

namespace Experius\Ponumber\Plugin\Magento\Sales\Api;
 
class OrderRepositoryInterface
{
    
    protected $orderExtensionFactory;
    
    public function __construct(
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $resultOrder
    ) {

        $resultOrder = $this->getExperiusPoNumber($resultOrder);
    
        return $resultOrder;
    }
    
    protected function getExperiusPoNumber(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();
        
        if ($extensionAttributes && $extensionAttributes->getExperiusPoNumber()) {
            return $order;
        }

        $poNumber = $order->getData('experius_po_number');

        $orderExtension = $extensionAttributes ? $extensionAttributes : $this->orderExtensionFactory->create();
        $orderExtension->setExperiusPoNumber($poNumber);
        $order->setExtensionAttributes($orderExtension);

        return $order;
    }
    
    public function afterGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Model\ResourceModel\Order\Collection $resultOrder
    ) {
        foreach ($resultOrder->getItems() as $order) {
            $this->afterGet($subject, $order);
        }
        return $resultOrder;
    }
}
