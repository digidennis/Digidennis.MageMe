<?php
/**
 * Created by PhpStorm.
 * User: W10
 * Date: 20-06-2016
 * Time: 00:55
 */

namespace Digidennis\MageMe\DataSource;

use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;

class ProductListDataSource extends AbstractDataSource
{
    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Service\MageProductService
     */
    protected $productService;

    /**
     * @var string
     */
    static protected $identifier = 'digidennis-mageme-productlist';

    /**
     * Get data
     *
     * @param NodeInterface $node The node that is currently edited (optional)
     * @param array $arguments Additional arguments (key / value)
     * @return array JSON serializable data
     */
    public function getData(NodeInterface $node = NULL, array $arguments)
    {
        $this->productService->initializeObject();
        $products = \Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('id')
            ->addAttributeToSelect('type')
        ;
        $result = array();
        foreach ($products as $product )
        {
            $result[] = array(
                'value' => $product->getId(),
                'label' => $product->getName(),
            );
        }
        return $result;
    }
}