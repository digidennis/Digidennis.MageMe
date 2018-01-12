<?php
namespace Digidennis\MageMe\Domain\Model;

/*
 * This file is part of the Digidennis.MageMe package.
 */

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use Digidennis\MageMe\Magento\MageQuoteWrapper;
/**
 * @Flow\Entity
 */
class Quote
{
    /**
     * @var string
     */
    protected $email;
    /**
     * @var boolean
     */
    protected $picked;
    /**
     * @var boolean
     */
    protected $dispatched;
    /**
     * @var array
     */
    protected $serializedQuote;
    /**
     * @var \DateTime
     */
    protected $date;


    /**
     * Quote constructor.
     */
    public function __construct()
    {
        $this->picked = $this->dispatched = false;
        $this->date = New \DateTime();
    }
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    /**
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
    /**
     * @return boolean
     */
    public function getPicked()
    {
        return $this->picked;
    }
    /**
     * @param boolean $picked
     * @return void
     */
    public function setPicked($picked)
    {
        $this->picked = $picked;
    }
    /**
     * @return boolean
     */
    public function getDispatched()
    {
        return $this->dispatched;
    }
    /**
     * @param boolean $dispatched
     * @return void
     */
    public function setDispatched($dispatched)
    {
        $this->dispatched = $dispatched;
    }
    /**
     * @return array
     */
    public function getSerializedQuote()
    {
        return $this->serializedQuote;
    }
    /**
     * @param array $serializedQuote
     * @return void
     */
    public function setSerializedQuote(array $serializedQuote)
    {
        $this->serializedQuote = $serializedQuote;
    }
    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
    /**
     * @param \DateTime $date
     * @return void
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

}
