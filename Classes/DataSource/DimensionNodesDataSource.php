<?php
/**
 * Created by PhpStorm.
 * User: W10
 * Date: 18-06-2016
 * Time: 20:18
 */

namespace Digidennis\MageMe\DataSource;

use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;

class DimensionNodesDataSource extends AbstractDataSource
{
    /**
     * @var string
     */
    static protected $identifier = 'digidennis-mageme-dimensionnodes';

    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Service\MageProductService
     */
    protected $productService;
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

        $result = array();
        $dimensions = $this->productService->getDimensionsForProduct($productnode->getProperty('productid'))->getData();
        foreach ($dimensions as $dim )
        {

            if( is_null( $dim['optiontitle']) && is_null($dim['typetitle']) )
                $label = 'Global, ';
            else
                $label = $dim['optiontitle'] . '-' . $dim['typetitle'] .', ';

            $result[] = array(
                'value' => $dim['dimension_id'],
                'label' => $label . $dim['label'],
            );
        }
        return $result;
    }
}