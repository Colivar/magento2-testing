<?php

namespace Experius\Ponumber\Observer\Sales;
 
use Magento\Framework\Event\ObserverInterface;

class ModelServiceQuoteSubmitBefore implements ObserverInterface
{

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
         $observer->getEvent()->getOrder()->setExperiusPoNumber($observer->getEvent()->getQuote()->getExperiusPoNumber());
    }
}
