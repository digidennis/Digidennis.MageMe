<?php

namespace Digidennis\MageMe\DataSource;

use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Eel\FlowQuery\FlowQuery;

class ProductOptionsDataSource extends AbstractDataSource
{
    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Service\MageProductService
     */
    protected $productService;

    /**
     * @var string
     */
    static protected $identifier = 'digidennis-mageme-productoptions';

    /**
     * Get data
     *
     * @param NodeInterface $node The node that is currently edited (optional)
     * @param array $arguments Additional arguments (key / value)
     * @return array JSON serializable data
     */
    public function getData(NodeInterface $node = NULL, array $arguments)
    {
        $query = new FlowQuery([$node]);
        $productnode = $query->parents('[instanceof Digidennis.MageMe:ProductPage]')->get(0);

        if( !$productnode )
            return array();

        $product = $this->productService->getProductById($productnode->getProperty('productid'));

        $options = $product->getOptions();
        $result = array();
        foreach ($options as $option )
        {
            $result[] = array(
                'value' => $option->getOptionId(),
                'label' => $option->getDefaultTitle(),
                'type' => $option->getType()
            );
        }
        return $result;
    }
}