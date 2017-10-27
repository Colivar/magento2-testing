<?php

namespace Experius\Ponumber\Observer\Core;
 
class LayoutRenderElement implements \Magento\Framework\Event\ObserverInterface
{

    protected $_objectManager;
        
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager)
    {
        $this->_objectManager = $objectmanager;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        if ($observer->getElementName() == 'order_info') {
            $block = $observer->getLayout()->getBlock($observer->getElementName());
            $order = $block->getOrder();
            $customBlock = $this->_objectManager->create('Experius\Ponumber\Block\Adminhtml\Sales\Order\View\Ponumber');
            $customBlock->setOrder($order);
            $customBlock->setTemplate('Experius_Ponumber::sales/order/view/ponumber.phtml');
            $html = $observer->getTransport()->getOutput() . $customBlock->toHtml();
            $observer->getTransport()->setOutput($html);
        }
        
        if ($observer->getElementName() == 'form_account') {
            $block = $observer->getLayout()->getBlock($observer->getElementName());
            $quote = $block->getQuote();
            $customBlock = $this->_objectManager->create('Experius\Ponumber\Block\Adminhtml\Sales\Order\Create\Ponumber');
            $customBlock->setQuote($quote);
            $customBlock->setTemplate('Experius_Ponumber::sales/order/create/ponumber.phtml');
            $html = $observer->getTransport()->getOutput() . $customBlock->toHtml();
            $observer->getTransport()->setOutput($html);
        }
        
        //$html = $observer->getTransport()->getOutput() . '>>>' .$observer->getElementName();
        //$observer->getTransport()->setOutput($html);
    }
}
