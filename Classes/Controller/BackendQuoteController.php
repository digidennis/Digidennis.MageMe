<?php
/**
 * Created by PhpStorm.
 * User: digid
 * Date: 28-11-2016
 * Time: 18:49
 */

namespace Digidennis\MageMe\Controller;


use Digidennis\MageMe\Domain\Helper\QuoteHelper;
use Digidennis\MageMe\Domain\Model\Quote;
use Digidennis\MageMe\Magento\MageQuoteWrapper;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;


class BackendQuoteController extends ActionController
{
    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Service\MageProductService
     */
    protected $productService;
    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Service\MageCartService
     */
    protected $cartService;
    /**
     * @Flow\Inject
     * @var \Digidennis\MageMe\Domain\Repository\QuoteRepository
     */
    protected $quoteRepository;
    /**
     * @Flow\Inject
     * @var \Neos\Flow\Persistence\PersistenceManagerInterface
     */
    protected $persistenceManager;
    /**
     * Index
     */
    public function indexAction()
    {
        $this->view->assignMultiple(
            array(
                'productselect' => $this->getProductList(),
                'quote' => new MageQuoteWrapper(),
            )
        );
    }
    /**
     * @param MageQuoteWrapper $quote
     * @param array $items
     * @param string $email
     * @param string $name
     * @param integer $productid
     * @throws \Neos\Flow\Exception
     * @throws \Neos\Flow\Mvc\Exception\ForwardException
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function updateAction( $quote = null, $items = null, $email = null, $name = null, $productid = null )
    {
        if( $this->request->hasArgument('Send') )
        {
            $this->sendAction($quote, $email, $name );
        }
        elseif ( $this->request->hasArgument('Add'))
        {
            $this->addItemAction($quote, $productid );
        }
        else
        {
            /*
            $myQuote = $this->quoteRepository->findNewestUnPickedByEmail('digidennis3@gmail.com');
            $mageQuote = QuoteHelper::mageQuoteFromQuote($myQuote, $this->productService);
            \Neos\Flow\var_dump($mageQuote);
            */
            foreach ($items as $id => $values )
            {
                $item = $quote->unWrap()->getItemById($id);
                foreach ($values['children'] as $childid => $childvalues )
                {
                    $child = $quote->unWrap()->getItemById($childid);
                    $child->setOriginalCustomPrice($childvalues['priceInclTax'])->setCustomPrice($childvalues['priceInclTax']);
                    $child->save();
                }
                $item->setQty($values['qty']);
                $item->setOriginalCustomPrice($values['priceInclTax'])->setCustomPrice($values['priceInclTax']);
                $item->save();
            }
            $quote->unWrap()->collectTotals();
        }
        $this->forward('index');
    }
    /**
     * @param MageQuoteWrapper $quote
     * @param integer $productid
     * @throws \Neos\Flow\Exception
     */
    public function addItemAction( $quote = null, $productid = null)
    {
        $product = $this->productService->getProductById($productid);
        $quoteItem = \Mage::getModel('sales/quote_item')->setProduct($product)->setQty(1);
        $quote->quote->addItem($quoteItem);
        $quote->quote->collectTotals()->save();
    }

    /**
     * @param MageQuoteWrapper $quote
     * @param string $email
     * @param string $name
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function sendAction($quote = null, $email = null, $name = null )
    {
        $ourQuote = new Quote();
        $ourQuote->setEmail($email);
        $ourQuote->setSerializedQuote(QuoteHelper::mageQuoteToDataArray($quote));
        $this->quoteRepository->add($ourQuote);

        $mail = new \Neos\SwiftMailer\Message();

        $mail
            ->setFrom(array('service@kbh-skum.dk' => 'KBH SKUM ApS'))
            ->setTo(array($email => $name ))
            ->setSubject('Tilbud fra KBH SKUM');

        $message = "Hej {$name}
        
Hermed fremsendes tilbud.
Klik på nedenstående link som vil tage dig til Indkøbskurven på kbh-skum.dk.
        
https://kbh-skum.dk/mageme/cart/quote?quoteId={$this->persistenceManager->getIdentifierByObject($ourQuote)}";
        $mail->setBody($message, 'text/plain');
        $mail->send();
    }

    /**
     * @param integer $itemId
     * @throws \Neos\Flow\Mvc\Exception\ForwardException
     */
    public function removeItemAction( $itemId )
    {
        $quote = $this->cartService->getQuote();
        $quote->removeItem($itemId);
        $quote->save();
        $this->forward('index');
    }
    /**
     * getProductList
     * @return array data
     */
    private function getProductList()
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
            $result[$product->getId()] = $product->getName();
        }
        return $result;
    }
}