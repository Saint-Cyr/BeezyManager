<?php

namespace TransactionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CanceledSTransaction
 *
 * @ORM\Table(name="canceled_s_transaction")
 * @ORM\Entity(repositoryClass="TransactionBundle\Repository\CanceledSTransactionRepository")
 */
class CanceledSTransaction
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
     * @var float
     *
     * @ORM\Column(name="totalAmount", type="float")
     */
    private $totalAmount;
    
    /**
     * @var float
     *
     * @ORM\Column(name="branchId", type="integer", nullable=true)
     */
    private $branchId;
    
    /**
     * @var float
     *
     * @ORM\Column(name="other", type="string", nullable=true)
     */
    private $other;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="canceledAt", type="datetime", nullable=true)
     */
    private $canceledAt;

    /**
     * @var array
     *
     * @ORM\Column(name="sales", type="array")
     */
    private $sales;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set totalAmount
     *
     * @param float $totalAmount
     *
     * @return CanceledSTransaction
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * Get totalAmount
     *
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return CanceledSTransaction
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
     * Set sales
     *
     * @param array $sales
     *
     * @return CanceledSTransaction
     */
    public function setSales($sales)
    {
        $this->sales = $sales;

        return $this;
    }

    /**
     * Get sales
     *
     * @return array
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * Set branchId
     *
     * @param integer $branchId
     *
     * @return CanceledSTransaction
     */
    public function setBranchId($branchId)
    {
        $this->branchId = $branchId;

        return $this;
    }

    /**
     * Get branchId
     *
     * @return integer
     */
    public function getBranchId()
    {
        return $this->branchId;
    }

    /**
     * Set canceledAt
     *
     * @param \DateTime $canceledAt
     *
     * @return CanceledSTransaction
     */
    public function setCanceledAt($canceledAt)
    {
        $this->canceledAt = $canceledAt;

        return $this;
    }

    /**
     * Get canceledAt
     *
     * @return \DateTime
     */
    public function getCanceledAt()
    {
        return $this->canceledAt;
    }

    /**
     * Set other
     *
     * @param string $other
     *
     * @return CanceledSTransaction
     */
    public function setOther($other)
    {
        $this->other = $other;

        return $this;
    }

    /**
     * Get other
     *
     * @return string
     */
    public function getOther()
    {
        return $this->other;
    }
}
