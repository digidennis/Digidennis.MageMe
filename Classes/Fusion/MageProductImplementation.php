<?php

namespace Digidennis\MageMe\Fusion;

use Neos\Flow\Annotations as Flow;
use Neos\Fusion\Exception as TypoScriptException;
use Neos\Fusion\FusionObjects\TemplateImplementation;

class MageProductImplementation extends TemplateImplementation
{
    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Service\MageProductService
     */
    protected $productService;

    /**
     * Magento product cache
     * @var mixed
     */
    protected $mageProduct;

    /**
     * @return mixed
     */
    protected function getMageProduct()
    {
        if (is_null($this->mageProduct))
        {
            $id = $this->fusionValue('productid');
            $this->mageProduct = $this->productService->getProductById($id);
        }
        return $this->mageProduct;
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->productService->getCurrencySymbol();
    }

    /**
     * @return float
     */
    public function getBasePrice()
    {
        return floatval($this->getMageProduct()->getPrice());
    }

    /**
     * @return string
     */
    public function getProductType()
    {
        return $this->getMageProduct()->getTypeId();
    }

    /**
     * @return string
     */
    public function getProductReturnPolicy()
    {
        return $this->getMageProduct()->getAttributeText('product_return_policy');
    }

    /*
     * @return string
     */
    public function getDeliveryString( )
    {
        $string = 'Afsendes: ';
        $min = $this->getMageProduct()->getDeliveryDaysMin();
        $max = $this->getMageProduct()->getDeliveryDaysMax();
        if( $min != $max)
            $string .=  $min . '-';
        $string .= $max . ' hverdage';
        return $string;
    }

    /*
     * @return string
     */
    public function getSKU()
    {
        return $this->getMageProduct()->getSku();
    }

    /**
     * @return array
     */
    public function getStockItem()
    {
        $item = $this->productService->getModel('cataloginventory/stock_item')->loadByProduct($this->getMageProduct());
        return array(
            'qty' => $item->getQty(),
            'minQty' => $item->getMinQty(),
            'backorders' => $item->getBackorders(),
            'isInStock' => $item->getIsInStock(),
            'manageStock' => $item->getManageStock(),
        );
    }

    public function getHasDimensions()
    {
        return $this->productService
            ->helper('digidennis_dimensionit/dimension')
            ->getDimensionInProductCount($this->getMageProduct()) > 0;
    }

    public function getStaticPrice()
    {
        if( $this->getHasDimensions() && $this->getMageProduct()->getDimensionInitialprice() <> '' )
            return 'Fra ' . number_format( $this->getMageProduct()->getDimensionInitialprice(), 0, ",", ".") . ' kr';

        $priceModel = $this->getMageProduct()->getPriceModel();
        switch ( $this->getProductType() )
        {
            case 'bundle':
                $prices = $priceModel->getTotalPrices($this->getMageProduct(), null, true, false);
                return 'Fra ' .$prices[0];
            case 'configurable':
                return false;
            case 'simple':
                return 'kr ' . (string)floor($priceModel->getFinalPrice(1, $this->getMageProduct()));

        }
    }
}