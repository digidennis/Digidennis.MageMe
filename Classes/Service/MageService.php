<?php
/**
 * Created by PhpStorm.
 * User: digid
 * Date: 20-04-2016
 * Time: 00:13
 */

namespace Digidennis\MageMe\Service;
use Neos\Flow\Annotations as Flow;

abstract class MageService
{
    /**
     * @Flow\InjectConfiguration(path="magefile")
     * @var string
     */
    protected $magefile;

    /**
     * @var \Mage_Core_Model_Session
     */
    protected $magesession;

    public function initializeObject()
    {
        include_once ($this->magefile);
        \Mage::app('default')->setCurrentStore(1)->loadArea('frontend');
        $this->magesession = \Mage::getSingleton('core/session', array('name' => 'frontend'));

        /*
        if ( Mage::getSingleton('customer/session')->isLoggedIn()) {
            return;
        }*/
        /** @var $checkoutSession Mage_Checkout_Model_Session */
        $checkoutSession = \Mage::getSingleton('checkout/session');
        if (\Mage::helper('persistent')->isShoppingCartPersist()) {
            $checkoutSession->setCustomer(
                \Mage::getModel('customer/customer')->load( \Mage::helper('persistent/session')->getSession()->getCustomerId() )
            );
            if (!$checkoutSession->hasQuote()) {
                $checkoutSession->getQuote();
            }
        }

    }
}