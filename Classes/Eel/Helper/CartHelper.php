<?php
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 09-05-16
 * Time: 15:24
 */

namespace Digidennis\MageMe\Eel\Helper;

use Neos\Flow\Annotations as Flow;
use Neos\Eel\ProtectedContextAwareInterface;

class CartHelper implements ProtectedContextAwareInterface
{

    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Service\MageCartService
     */
    protected $cartService;

    /**
     * @return \Mage_Checkout_Model_Cart
     */
    public function getCart()
    {
        return $this->cartService->getCart();
    }

    /**
     * @return \Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->cartService->getParsedQuote();
    }

    /**
     * @return int
     */
    public function getQuoteCount()
    {
        return $this->cartService->getQuoteCount();
    }
    
    /**
     * All methods are considered safe, i.e. can be executed from within Eel
     *
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        return TRUE;
    }

}