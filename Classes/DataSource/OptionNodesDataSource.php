<?php

namespace Digidennis\MageMe\DataSource;

use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;

class OptionNodesDataSource extends AbstractDataSource
{
    /**
     * @var string
     */
    static protected $identifier = 'digidennis-mageme-optionnodes';

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
        $optionnodes = $query->parents('[instanceof Digidennis.MageMe:ProductPage]')->find('[instanceof Digidennis.MageMe:Option]')->get();
        $parentoption = $query->parents('[instanceof Digidennis.MageMe:Option]')->get(0);
        $productnode = $query->parents('[instanceof Digidennis.MageMe:ProductPage]')->get(0);
        $result = array();
        foreach( $optionnodes as $option )
        {
            if($option->getIdentifier() != $parentoption->getIdentifier() )
            {
                $mageoption = $this->productService->getOptionInProduct($option->getProperty('optionid'), $productnode->getProperty('productid'));
                $result[] = array(
                    'value' => $mageoption->getOptionId(),
                    'label' => $mageoption->getDefaultTitle()
                );
            }
        }
        return $result;
    }
}