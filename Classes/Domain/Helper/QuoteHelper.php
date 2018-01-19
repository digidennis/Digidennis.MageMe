<?php
/**
 * Created by PhpStorm.
 * User: digid
 * Date: 20-12-2017
 * Time: 22:45
 */
namespace Digidennis\MageMe\Domain\Helper;

use Digidennis\MageMe\Domain\Model\Quote;
use Digidennis\MageMe\Magento\MageQuoteWrapper;


class QuoteHelper
{
    /**
     * 'inflate' data
     * @param Quote $quote
     * @return \Mage_Sales_Model_Quote
     */
    public static function mageQuoteFromQuote(Quote $quote, $productService)
    {
        $mageQuote = \Mage::getModel('sales/quote');
        $mageQuote->setData($quote->getSerializedQuote()['data']);
        foreach ($quote->getSerializedQuote()['items'] as $item)
        {
            $mageItem = $mageQuote->getItemsCollection()->getNewEmptyItem();
            $mageItem->setData($item['data']);
            $mageitemproduct = $productService->getProductById($item['data']['product_id']);
            foreach($item['options'] as $option)
            {
                $mageItem->addOption($option);
                $mageitemproduct->getOptionInstance()->addOption($option);
            }
            $mageItem->setProduct($mageitemproduct );
            $mageQuote->addItem($mageItem);
        }
        return $mageQuote;
    }

    /**
     * 'deflate' data
     * sanitize data from quote+items+options, remove ids and objs'
     * @param MageQuoteWrapper $quote
     * @return array
     */
    public static function mageQuoteToDataArray(MageQuoteWrapper $quote)
    {
        $itemsdata = array();
        $dataarray = ['data' => $quote->unWrap()->getData()];

        unset($dataarray['data']['entity_id']);
        unset($dataarray['data']['customer_id]']);
        unset($dataarray['data']['customer_tax_class_id']);
        unset($dataarray['data']['customer_group_id']);
        unset($dataarray['data']['customer_email']);
        unset($dataarray['data']['customer_firstname']);
        unset($dataarray['data']['customer_lastname']);
        $dataarray['data']['customer_is_guest'] = 1;

        foreach ($quote->unWrap()->getAllItems() as $item )
        {
            $itemdata = ['data' => $item->getData()];
            unset($itemdata['data']['item_id']);
            unset($itemdata['data']['quote_id']);
            unset($itemdata['data']['product']);
            $optionsdata = array();
            foreach ( $item->getOptions() as $option)
            {
                $optionsdata[] = $option->getData();
                unset($optionsdata[count($optionsdata)-1]['option_id']);
                unset($optionsdata[count($optionsdata)-1]['item_id']);
            }
            $itemdata['options'] = $optionsdata;
            $itemsdata[] = $itemdata;
        }
        $dataarray['items'] = $itemsdata;
        return $dataarray;
    }
}