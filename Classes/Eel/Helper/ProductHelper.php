<?php
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 09-05-16
 * Time: 15:24
 */

namespace Digidennis\MageMe\Eel\Helper;

use Neos\ContentRepository\Exception;
use Neos\Flow\Annotations as Flow;
use Neos\Eel\ProtectedContextAwareInterface;

class ProductHelper implements ProtectedContextAwareInterface
{

    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Service\MageProductService
     */
    protected $productService;
    
    /**
     * @param $sku
     * @return \Mage_Catalog_Model_Product
     */
    public function getProduct($sku)
    {
        return $this->productService->getProductById(
            $this->productService->getIdBySku($sku)
        );
    }

    public function getCurrencySymbol()
    {
        return $this->productService->getCurrencySymbol();
    }
    /**
     * @param $id
     * @return array
     */
    public function optionSelection($id,$optionid)
    {
        return $this->productService->getSimpleOptionSelection($id, $optionid);
    }

    /**
     * @param $productid
     * @return array
     */
    public function getSlotForProduct($productid)
    {
        return $this->productService->getSlotForProduct($productid);
    }

    /**
     * @param $productid
     * @return array
     */
    public function getSlotsForOption($optionid)
    {
        return $this->productService->getSlotsForOption($optionid);
    }

    /**
     * @param $id
     * @return float
     */
    public function getBasePrice($id)
    {
        return floatval($this->productService->getProductById($id)->getPrice());
    }   
    
    /**
     * @param $id
     * @return string
     */
    public function getBasePriceType($id)
    {
        return $this->productService->getProductById($id)->getTypeId();
    }
    /**
     * @param $id
     * @return string
     */
    public function getProductReturnPolicy($id)
    {
        return $this->productService->getProductById($id)->getDefaultValue('product_return_policy');
    }
    /**
     * @param $id
     * @return string
     */
    public function getDeliveryDaysString($id)
    {
        return $this->productService->getProductById($id)->getDeliveryDaysString();
    }
    /**
     * @param $id
     * @return mixed
     */
    public function getStockItem($id)
    {
        $item = $this->productService->getStockItem($id);
        return array(
            'qty' => $item->getQty(),
            'minQty' => $item->getMinQty(),
            'backorders' => $item->getBackorders(),
            'isInStock' => $item->getIsInStock(),
            'manageStock' => $item->getManageStock(),
        );
    }

    public function getOptionInProduct($optionid, $productid)
    {
        return $this->productService->getOptionInProduct($optionid,$productid)->getData();
    }

    public function getDimension($dimensionid)
    {
        return $this->productService->getDimension($dimensionid);
    }

    public function getSlot($slotid)
    {
        return $this->productService->getSlot($slotid);
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