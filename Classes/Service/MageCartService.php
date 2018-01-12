<?php
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 02-05-16
 * Time: 08:58
 */

namespace Digidennis\MageMe\Service;

use Neos\Flow\Annotations as Flow;


/**
 * @Flow\Scope("singleton")
 */
class MageCartService extends MageService
{

    /**
     * @var \Mage_Checkout_Model_Session
     */
    protected $checkoutSession;

    //parsed quote cache
    protected $parsedQuote;
    
    public function initializeObject()
    {
        parent::initializeObject();
        $this->checkoutSession = \Mage::getSingleton('checkout/session');
    }

    /**
     * @return \Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();   
    }
    /**
     * @return \Mage_Sales_Model_Quote
     */
    public function getQuoteById($id)
    {
        $quote = \Mage::getModel('sales/quote')->load($id);
        return $quote;
    }

    /**
     * @return \Mage_Checkout_Model_Cart
     */
    public function getCart()
    {
        return \Mage::getSingleton('checkout/cart');
    }

    /**
     * @return \Mage_Checkout_Model_Session
     */
    public function getSession()
    {
        return $this->checkoutSession;
    }

    /**
     * Get List Of Quotes
     * @return array
     */
    public function getListOfQuotes()
    {
        $collection =  \Mage::getModel('sales/quote')->getCollection()->setPageSize(20)->setCurPage(10);
        $result = array();
        foreach ( $collection as $quote ){
            $result[$quote->getId()] = $quote->getId();
        }
        return $result;
    }


    /**
     * Get Empty Quote
     * @return mixed
     */
    public function getEmptyQuote()
    {
        return false;
    }

    public function getParsedQuote()
    {
        if(is_null($this->parsedQuote))
        {
            $quote = $this->getQuote();

            $this->parsedQuote = array(
                'shipping' => $this->parseShipping($quote),
                'grandTotal' => floor(floatval($quote->getGrandTotal())),
                'itemCount' => $quote->getItemsQty(),
                'isChanged' => $quote->getIsChanged(),
                'isActive' => $quote->getIsActive(),
                'id' => $quote->getId(),
                'items' => array()
            );

            $quote_items = $quote->getAllVisibleItems();
            $this->parseQuoteItem($quote_items, $this->parsedQuote['items']);
        }
        return $this->parsedQuote;
    }

    /**
     * @return string
     */
    public function getQuoteCount()
    {
        return $this->getCart()->getItemsCount();
    }

    protected function parseShipping( $quote )
    {
        $country = 'DK';
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCountryId($country)
            ->setCollectShippingRates(true)
            ->collectShippingRates();

        $rates = $shippingAddress->getShippingRatesCollection();
        $parsedrates = array();
        //$shippingAddress->setShippingMethod($rates->getCode());
        //$quote->collectTotals();
        foreach($rates as $rate)
        {
            var_dump($rate);
            $parsedrates[] = [
                'carrierTitle' => $rate->getCarrierTitle(),
                'methodTitle' => $rate->getMethodTitle(),
                'methodDescription' => $rate->getMethodDescription(),
                'price' => floor(floatval($rate->getPrice()))
            ];
        }

        return $parsedrates;
    }

    protected function parseQuoteItem( $itemarray, &$bucket )
    {
        foreach ($itemarray as $item)
        {
            $parsedQuoteItem = array(
                'options' => \Mage::helper('catalog/product_configuration')->getOptions($item),
                'subTotal' => $item->getBasePrice(),
                'subTotalTax' => floor(floatval($item->getPriceInclTax())),
                'rowTotalTax' => floor(floatval($item->getRowTotalInclTax())),
                'rowTax' => $item->getTaxAmount(),
                'qty' => $item->getQty(),
                'name' => $item->getName(),
                'itemId' => $item->getId(),
            );

            $bucket[] = $parsedQuoteItem;
        }
    }
}