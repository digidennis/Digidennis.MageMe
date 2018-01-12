<?php
/**
 * Created by PhpStorm.
 * User: digid
 * Date: 20-04-2016
 * Time: 00:12
 */

namespace Digidennis\MageMe\Service;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Exception;


/**
 * @Flow\Scope("singleton")
 */
class MageProductService extends MageService
{

    public function formatPrice($amount)
    {
        return \Mage::helper('core')->currency($amount, true, false);
    }

    public function getCurrencySymbol()
    {
        return \Mage::app()->getLocale()->currency(\Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
    }
    
    /**
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function getProductById($id)
    {
        $result = \Mage::getModel('catalog/product')
            ->setStoreId(\Mage::app()->getStore()->getId())
            ->load($id);
        if(!$result->getId())
            throw new Exception('Product not found');
        
        return $result;
    }

    public function getStockItem($id)
    {
        return \Mage::getModel('cataloginventory/stock_item')->loadByProduct($this->getProductById($id));
    }

    public function getSimpleOptionSelection($id, $optionid)
    {
        $product = $this->getProductById($id);
        $option = $product->getOptionById($optionid);

        if(!$option)
            return;

        $selection = $option->getValues();
        $result = array(
            'name' => $option->getDefaultTitle(),
            'isRequired' => $option->getIsRequire() === '1' ? true : false,
            'isMulti' => $option->isMultipleType(),
            'type' => $option->getType(),
            'optionId' => $option->getOptionId(),
            'label' => $option->getTitle(),
            'items' => array(),
            'hasDefault' => false
        );
        foreach ($selection as $item)
        {
            $result['items'][] = array(
                'selectionId' => $item->getOptionTypeId(),
                'price' => $item->getPrice(),
                'priceType' => $item->getPriceType(),
                'name' => $item->getDefaultTitle(),
                'isDefault' => false
            );
        }
        return $result;
    }

    public function getSlotForProduct($product_id)
    {
        $productslot = \Mage::getModel('digidennis_dimensionit/slot')
            ->getCollection()
            ->filterForProduct($product_id);
        $return = false;

        if($productslot)
        {
            $return = $productslot->getFirstItem()->getData();
            $return['dimensions'] = $productslot->getFirstItem()->getDimensions()->getData();
        }

        return $return;
    }

    public function getSlotsForOption($option_id)
    {
        $optionslots = \Mage::getModel('digidennis_dimensionit/slot')
            ->getCollection()
            ->filterForOption($option_id);
        $return = false;

        if($optionslots)
        {
            foreach ($optionslots as $slot)
            {
                $slotdata = $slot->getData();
                $slotdata['dimensions'] = $slot->getDimensions()->getData();
                $return[] = $slotdata;
            }
        }

        return $return;
    }

    public function getSlot($slotid)
    {
        $slot = \Mage::getModel('digidennis_dimensionit/slot')->load($slotid);
        return $slot->getData();
    }

    public function getDimensionsForProduct($product_id)
    {
        $dimensions = \Mage::getModel('digidennis_dimensionit/dimension')
            ->getCollection()
            ->filterForProduct($product_id);
        return $dimensions;
    }

    public function getDimension($dimensionid)
    {
        $dimension = \Mage::getModel('digidennis_dimensionit/dimension')->load($dimensionid);
        return $dimension->getData();
    }

    public function getOptionInProduct($option_id, $product_id)
    {
        $product = $this->getProductById($product_id);
        $option = $product->getOptionById($option_id);
        return $option;
    }
    /**
     * @param $sku
     * @return integer
     */
    public function getIdBySku($sku)
    {
        return \Mage::getModel('catalog/product')->getIdBySku($sku);
    }

    public function getModel($model)
    {
        return \Mage::getModel($model);
    }

    public function helper($helper)
    {
        return \Mage::helper($helper);
    }
    
}