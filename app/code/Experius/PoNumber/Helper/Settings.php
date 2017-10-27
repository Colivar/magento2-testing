<?php
namespace Experius\Ponumber\Helper;

class Settings extends \Experius\Core\Helper\Settings
{
    
    protected $customerSession;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_storeManager = $storeManager;   
        $this->customerSession = $customerSession;     
        parent::__construct($context,$storeManager);
    }
    
       
    public function enabled()
    {
        return $this->scopeConfig->getValue('experius_ponumber/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function isRequired() {
        return ($this->isRequiredGeneral() || ($this->isRequiredForCustomer() && $this->isRequiredForCustomerUsed()));       
    }
    
    public function isRequiredGeneral() {
        return $this->scopeConfig->getValue('experius_ponumber/general/required', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function isRequiredForCustomer() {
        return $this->customerSession->getCustomer()->getData('experius_po_number_required');        
    }
    
    public function isRequiredForCustomerUsed() {
        return $this->scopeConfig->getValue('experius_ponumber/general/requiredoverride', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);   
    }
    
}
