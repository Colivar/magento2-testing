<?php

namespace Experius\Ponumber\Controller\Adminhtml\Ajax;
 
class Save extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $this->_getQuote()->setExperiusPoNumber($this->getRequest()->getPost('ponumber'))->save();
            return $this->jsonResponse('succes');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
    
    protected function _getSession()
    {
        return $this->_objectManager->get('Magento\Backend\Model\Session\Quote');
    }

    protected function _getQuote()
    {
        return $this->_getSession()->getQuote();
    }
}
