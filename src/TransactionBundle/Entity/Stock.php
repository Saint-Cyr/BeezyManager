<?php

namespace TransactionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stock
 *
 * @ORM\Table(name="stock")
 * @ORM\Entity(repositoryClass="TransactionBundle\Repository\StockRepository")
 */
class Stock
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="updateOther", type="string", length=255, nullable=true)
     */
    private $updateOther;
    
    /**
     * @var string
     *
     * @ORM\Column(name="value", type="integer", nullable=true)
     */
    private $value;
    
    /**
     * @var string
     *
     * @ORM\Column(name="stockedValue", type="integer", nullable=true)
     */
    private $stockedValue;
    
    /**
     * @var string
     *
     * @ORM\Column(name="destockedValue", type="integer", nullable=true)
     */
    private $destockedValue;
    
    /**
     * @var string
     *
     * @ORM\Column(name="stocked", type="boolean", nullable=true)
     */
    private $stocked;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tracked", type="boolean", nullable=true)
     */
    private $tracked;
    
    /**
     * @var string
     *
     * @ORM\Column(name="destocked", type="boolean", nullable=true)
     */
    private $destocked;
    
    /**
     * @var string
     *
     * @ORM\Column(name="alertLevel", type="integer", nullable=true)
     */
    private $alertLevel;
    
    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="stocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;
    
    /**
     * @ORM\ManyToOne(targetEntity="KmBundle\Entity\Branch", inversedBy="stocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $branch;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stockedAt", type="datetime", nullable=true)
     */
    private $stockedAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="destockedAt", type="datetime", nullable=true)
     */
    private $destockedAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="alertStockCreatedAt", type="datetime", nullable=true)
     */
    private $alertStockCreatedAt;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function isAlertStock()
    {
        if($this->getAlertLevel() >= $this->getValue()){
            //Reset the alertStockCreatedAt
            $this->setAlertStockCreatedAt(new \DateTime("now"));
            
            return true;
        }
        
        return false;
    }
    
    public function decreaseValue($quantity)
    {
        $this->value = $this->value - $quantity;
    }
    
    public function increaseValue($quantity)
    {
        $this->value = $this->value + $quantity;
    }
    
    public function __toString() {
        
        if($this->name){
            return $this->name;
        }
        
        return 'Stock';
        
    }
    
    public function __construct() {
        $this->setCreatedAt(new \DateTime("now"));
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Stock
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Stock
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param integer $value
     *
     * @return Stock
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set product
     *
     * @param \TransactionBundle\Entity\Product $product
     *
     * @return Stock
     */
    public function setProduct(\TransactionBundle\Entity\Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \TransactionBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set branch
     *
     * @param \KmBundle\Entity\Branch $branch
     *
     * @return Stock
     */
    public function setBranch(\KmBundle\Entity\Branch $branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch
     *
     * @return \KmBundle\Entity\Branch
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * Set alertLevel
     *
     * @param integer $alertLevel
     *
     * @return Stock
     */
    public function setAlertLevel($alertLevel)
    {
        $this->alertLevel = $alertLevel;

        return $this;
    }

    /**
     * Get alertLevel
     *
     * @return integer
     */
    public function getAlertLevel()
    {
        return $this->alertLevel;
    }

    /**
     * Set alertStockCreatedAt
     *
     * @param \DateTime $alertStockCreatedAt
     *
     * @return Stock
     */
    public function setAlertStockCreatedAt($alertStockCreatedAt)
    {
        $this->alertStockCreatedAt = $alertStockCreatedAt;

        return $this;
    }

    /**
     * Get alertStockCreatedAt
     *
     * @return \DateTime
     */
    public function getAlertStockCreatedAt()
    {
        return $this->alertStockCreatedAt;
    }

    /**
     * Set stocked
     *
     * @param boolean $stocked
     *
     * @return Stock
     */
    public function setStocked($stocked)
    {
        $this->stocked = $stocked;

        return $this;
    }

    /**
     * Get stocked
     *
     * @return boolean
     */
    public function getStocked()
    {
        return $this->stocked;
    }

    /**
     * Set destocked
     *
     * @param boolean $destocked
     *
     * @return Stock
     */
    public function setDestocked($destocked)
    {
        $this->destocked = $destocked;

        return $this;
    }

    /**
     * Get destocked
     *
     * @return boolean
     */
    public function getDestocked()
    {
        return $this->destocked;
    }

    /**
     * Set tracked
     *
     * @param boolean $tracked
     *
     * @return Stock
     */
    public function setTracked($tracked)
    {
        $this->tracked = $tracked;

        return $this;
    }

    /**
     * Get tracked
     *
     * @return boolean
     */
    public function isTracked()
    {
        return $this->tracked;
    }

    /**
     * Set updateOther
     *
     * @param string $updateOther
     *
     * @return Stock
     */
    public function setUpdateOther($updateOther)
    {
        $this->updateOther = $updateOther;

        return $this;
    }

    /**
     * Get updateOther
     *
     * @return string
     */
    public function getUpdateOther()
    {
        return $this->updateOther;
    }

    /**
     * Get tracked
     *
     * @return boolean
     */
    public function getTracked()
    {
        return $this->tracked;
    }

    /**
     * Set stockedValue
     *
     * @param integer $stockedValue
     *
     * @return Stock
     */
    public function setStockedValue($stockedValue)
    {
        $this->stockedValue = $stockedValue;

        return $this;
    }

    /**
     * Get stockedValue
     *
     * @return integer
     */
    public function getStockedValue()
    {
        return $this->stockedValue;
    }

    /**
     * Set destockedValue
     *
     * @param integer $destockedValue
     *
     * @return Stock
     */
    public function setDestockedValue($destockedValue)
    {
        $this->destockedValue = $destockedValue;

        return $this;
    }

    /**
     * Get destockedValue
     *
     * @return integer
     */
    public function getDestockedValue()
    {
        return $this->destockedValue;
    }

    /**
     * Set stockedAt
     *
     * @param \DateTime $stockedAt
     *
     * @return Stock
     */
    public function setStockedAt($stockedAt)
    {
        $this->stockedAt = $stockedAt;

        return $this;
    }

    /**
     * Get stockedAt
     *
     * @return \DateTime
     */
    public function getStockedAt()
    {
        return $this->stockedAt;
    }

    /**
     * Set destockedAt
     *
     * @param \DateTime $destockedAt
     *
     * @return Stock
     */
    public function setDestockedAt($destockedAt)
    {
        $this->destockedAt = $destockedAt;

        return $this;
    }

    /**
     * Get destockedAt
     *
     * @return \DateTime
     */
    public function getDestockedAt()
    {
        return $this->destockedAt;
    }
}
