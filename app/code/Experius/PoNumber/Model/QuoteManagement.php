<?php

namespace Experius\Ponumber\Model;

use Magento\Framework\Exception\CouldNotSaveException;

class QuoteManagement
{
    
    protected $quoteRepository;
    
    protected $quoteIdMaskFactory;
    
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }
   
    /**
     * {@inheritdoc}
     */
    public function setPonumber($cartId, $ponumber)
    {
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setExperiusPoNumber($ponumber);
        
        try {
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Cannot save quote'));
        }
        
        return $ponumber;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPonumber($cartId)
    {
        $quote = $this->quoteRepository->getActive($cartId);
        return $quote->getExperiusPoNumber();
    }
    
    /**
     * {@inheritdoc}
     */
    public function setPonumberGuest($cartId, $ponumber)
    {
        $cartId = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id')->getQuoteId();
        return $this->setPonumber($cartId, $ponumber);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPonumberGuest($cartId)
    {
        $cartId = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id')->getQuoteId();
        return $this->getPonumber($cartId);
    }
}
