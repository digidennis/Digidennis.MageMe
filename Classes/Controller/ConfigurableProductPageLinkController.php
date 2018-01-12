<?php
namespace Digidennis\MageMe\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Eel\FlowQuery\FlowQuery;
/**
 * An action controller for generic authentication in Flow
 *
 * @Flow\Scope("singleton")
 */
class ConfigurableProductPageLinkController extends ActionController
{
    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Service\MageProductService
     */
    protected $productService;

    public function indexAction()
    {
        $productid = $this->request->getInternalArgument('__productid');
        /** @var Neos\ContentRepository\Domain\Model\NodeInterface $site */
        $docnode = $this->request->getInternalArgument('__documentNode');

        $product = $this->productService->getProductById($productid);

        $bundleoptions = array();
        $options = array();
        foreach ($product->getOptions() as $option )
        {
            $options[] = $this->productService->getSimpleOptionSelection($productid, $option->getOptionId());
        }
        if( $product->getTypeId() == 'bundle' )
        {
            $type = $product->getTypeInstance(true);
            foreach( $type->getOptions($product) as $bundleoption )
            {
                $bundleoptions[] = $this->productService->getBundleOptionSelection($productid, $bundleoption->getOptionId());
            }
        }

        $query = new FlowQuery([$docnode]);


        $alldimensions = $query->parentsUntil('[instanceof Digidennis.SitePack:HomePage]')
            ->find("[instanceof Digidennis.MageMe:ProductPage][productid='{$productid}']")
            ->find("[instanceof Digidennis.MageMe:Dimension]")
            ->get();
        $dimensions = array();
        foreach ($alldimensions as $dim )
        {
            $dimensions[] = array(
                'initial' => $dim->getProperty('initial'),
                'min' => $dim->getProperty('min'),
                'max' => $dim->getProperty('max'),
                'step' => $dim->getProperty('step'),
                'unit' => $dim->getProperty('unit'),
                'multiplier' => $dim->getProperty('multiplier'),
                'label' =>  $dim->getParent()->getParent()->getProperty('optiontitle') . ' ' . $dim->getProperty('label'),
            );
        }

        $this->view->assign('productid', $productid);
        $this->view->assign('options', $options);
        $this->view->assign('bundleoptions', $bundleoptions);
        $this->view->assign('dimensions', $dimensions);
    }
}