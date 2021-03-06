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
     * @Flow\Inject
     * @var \Digidennis\MageMe\Service\MageProductService
     */
    protected $productService;
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
        if( $shippingAddress->getShippingRatesCollection() && $shippingAddress->getShippingRatesCollection()->getSize() == 0 )
        {
            $shippingAddress->setCountryId($country)
                ->setCollectShippingRates(true)
                ->collectShippingRates();
        }

        $rates = ($shippingAddress->getShippingRatesCollection()->toArray())['items'];
	    return $rates;
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
                'buyRequest' => $this->parseBuyRequest($item),
                'neosnode' => $this->productService->getNeosNodeFromProductId($item->getProductId())
            );

            $bucket[] = $parsedQuoteItem;
        }
    }

    protected function parseBuyRequest($item)
    {
        $parsedbuyrequest = array();
        $buyRequest = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
        if(count($buyRequest))
        {
            if(key_exists('dimensions', $buyRequest) )
            {
                foreach ($buyRequest['dimensions'] as $key => $value)
                {
                    $parsedbuyrequest['dimensions'][$key] = $value['value'];
                }
            }
            if(key_exists('options', $buyRequest) )
            {
                $parsedbuyrequest['options'] = $buyRequest['options'];
            }
        }
        $parsedbuyrequest['edit'] = $item->getItemId();
        return $parsedbuyrequest;
    }

}
