<?php
/**
 * Created by PhpStorm.
 * User: digid
 * Date: 19-12-2017
 * Time: 16:54
 */

namespace Digidennis\MageMe\Magento;
use Neos\Flow\Annotations as Flow;

/**
 * Class MageQuoteWrapper
 * @package Digidennis\MageMe\Magento
 */
class MageQuoteWrapper
{
    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Service\MageCartService
     */
    protected $cartService;

    /**
     * @var \Mage_Sales_Model_Quote
     */
    public $quote;

    public function initializeObject() {
        $this->quote = $this->cartService->getQuote();
    }

    public function unWrap()
    {
        return $this->cartService->getQuote();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->unWrap()->getId();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
       //
    }
}