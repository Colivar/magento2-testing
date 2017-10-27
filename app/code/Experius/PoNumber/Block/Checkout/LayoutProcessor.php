<?php


namespace Experius\Ponumber\Block\Checkout;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    
    protected $scopeConfig;
    protected $helper;
    
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Experius\Ponumber\Helper\Settings $helper
    ) {
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
    }

    public function process($result)
    {
        
        if ($this->helper->enabled()) {
            $ponumberToolTip = ($this->scopeConfig->getValue('experius_ponumber/general/tooltip', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) ? $this->scopeConfig->getValue('experius_ponumber/general/tooltip', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) : __('Enter your purchase order number');
            $ponumberTitle = ($this->scopeConfig->getValue('experius_ponumber/general/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) ? $this->scopeConfig->getValue('experius_ponumber/general/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) : __('Purchase Order Number');
        
            $poNumberField =
            ['experius_po_number'=>
                [
                    'component' => 'Experius_Ponumber/js/view/form/element/ponumber',
                    'config' => [
                        "customerScope" => 'experiusPonumberForm',
                        "template" => 'ui/form/field',
                        "elementTmpl" => 'ui/form/element/input',
                        "tooltip" => [
                            "description" => $ponumberToolTip
                        ]
                    ],
                    'provider' => 'checkoutProvider',
                    'dataScope' => 'experiusPonumberForm.experius_po_number',
                    'label' => $ponumberTitle,
                    'sortOrder' => '1',
                    'validation' => [
                        'required-entry' => ($this->helper->isRequired()) ? true : false,
                    ],
                ]
            ];
            
            $ponumberForm =
                [
                 'component'=>'Experius_Ponumber/js/view/form/ponumber',
                 'provider' => 'checkoutProvider',
                 'config'=> [
                    'template'=>'Experius_Ponumber/form'
                 ],
                 'children' =>[
                    'experius-ponumber-form-fieldset'=>[
                        'component'=>'uiComponent',
                        'displayArea'=>'custom-checkout-form-fields',
                        'children'=> $poNumberField
                    ]
                 ]
                    
                ];
            
            $result['components']['checkout']['children']['steps']['children']
                    ['shipping-step']['children']['shippingAddress']['children']
                        ['before-form']['children']['experius-ponumber-form-container'] = $ponumberForm;
        }
        
        return $result;
    }
}
