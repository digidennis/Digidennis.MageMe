<?php
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 02-05-16
 * Time: 15:11
 */

namespace Digidennis\MageMe\Controller;

use Digidennis\MageMe\Domain\Helper\QuoteHelper;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Neos\Service\LinkingService;
use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;

class CartController extends ActionController
{

    /**
     * @Flow\Inject
     * @var LinkingService
     */
    protected $linkingservice;

    /**
     * @Flow\Inject
     * @var ContextFactoryInterface
     */
    protected $contextFactory;

    /**
     * @Flow\InjectConfiguration(path="cartnodeidentifier")
     * @var string
     */
    protected $cartnodeidentifier;
    
    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Service\MageCartService
     */
    protected $cartService;

    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Service\MageProductService
     */
    protected $productService;

    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Domain\Repository\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @param NodeInterface $nodePath
     * @param array $bundle_option
     * @param array $options
     * @param int $qty
     * @param array $dimensions
     * @return mixed
     */
    public function addAction(NodeInterface $nodePath, $bundle_option = null, $options = null, $qty=1, $dimensions = null)
    {
        $cart = $this->cartService->getCart();
        $product = $this->productService->getProductById($nodePath->getProperty('productid'));
        $params = array(
            'qty' => $qty,
            'product' => $product->getId()
        );

        //sanitized is options without values == "-1"
        $sanitizedOptions = array();
        if($options)
        {
            foreach ($options as $key => $option)
            {
                if($option !== "-1" && !is_array($option))
                {
                   $sanitizedOptions[$key] = $option;
                }
                else if( is_array($option) )
                {
                    $innersanitized = array();
                    foreach ($option as $itemkey => $item)
                    {
                        if($item !== "-1")
                        {
                           $innersanitized[$itemkey] = $option[$itemkey];
                        }
                    }
                    if(count($innersanitized))
                        $sanitizedOptions[$key] = $innersanitized;
                }
            }
            $params['options'] = $sanitizedOptions;
        }


        if($dimensions)
            $params['dimensions'] = $dimensions;

        $cart->addProduct($product, $params);
        $cart->save();

        $this->cartService->getSession()->setCartWasUpdated(true);

        if ($this->request->getFormat() === 'json')
        {
            $data=array("ok", $cart->getItemsCount());
            return json_encode($data);
        }
        else
        {
            return $this->redirectToCartAction();
        }
    }

    /**
     * @param array $action
     * @param array $cart
     */
    public function updateAction( $action = null, $cart = null )
    {

        if(array_key_exists('itemDelete',$action))
            $cart[array_keys($action['itemDelete']['itemId'])[0]]['remove'] = true;
        $mageCart = $this->cartService->getCart()->updateItems($cart);
        $this->cartService->getCart()->save();
        $this->redirectToRequest($this->request->getReferringRequest());
    }

    /**
     * @param string $quoteId
     */
    public function quoteAction( $quoteId )
    {
        $this->cartService->initializeObject();

        $ourQuote = $this->quoteRepository->findByIdentifier($quoteId);
        $mageQuote = QuoteHelper::mageQuoteFromQuote($ourQuote, $this->productService);
        //$ourQuote->setPicked(true);
        //$this->quoteRepository->update($ourQuote);;
        $mageQuote->setStoreId(1)->setIsActive(1)->save();
        $this->cartService->getCart()->setQuote($mageQuote);
        $this->cartService->getCart()->save();
        $this->redirectToCartAction();
    }

    public function redirectToCartAction()
    {
        $context = $this->contextFactory->create(array('workspaceName' => 'live'));
        $node = $context->getNodeByIdentifier($this->cartnodeidentifier);
        $nodeuri = $this->linkingservice->createNodeUri($this->getControllerContext(), $node, null, 'html', true );
        $this->redirectToUri($nodeuri);
    }

    /**
     *
     */
    public function displayAction()
    {
        $quote = $this->cartService->getParsedQuote();
        $this->view->assign('quote',$quote);
    }
}