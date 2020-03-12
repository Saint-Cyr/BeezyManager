<?php

namespace TransactionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Report
 *
 * @ORM\Table(name="report")
 * @ORM\Entity(repositoryClass="TransactionBundle\Repository\ReportRepository")
 */
class Report
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
     * @var \DateTime
     *
     * @ORM\Column(name="initDate", type="datetime", nullable=true)
     */
    private $initDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finitDate", type="datetime", nullable=true)
     */
    private $finitDate;
    
    
    private $e1;
   
    private $e2;
    
    private $e3;

    private $type;

    public function getType()
    {
        return $this->type;
    }
    
    


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
     * Set initDate
     *
     * @param \DateTime $initDate
     *
     * @return Report
     */
    public function setInitDate($initDate)
    {
        $this->initDate = $initDate;

        return $this;
    }

    /**
     * Get initDate
     *
     * @return \DateTime
     */
    public function getInitDate()
    {
        return $this->initDate;
    }

    /**
     * Set finitDate
     *
     * @param \DateTime $finitDate
     *
     * @return Report
     */
    public function setFinitDate($finitDate)
    {
        $this->finitDate = $finitDate;

        return $this;
    }

    /**
     * Get finitDate
     *
     * @return \DateTime
     */
    public function getFinitDate()
    {
        return $this->finitDate;
    }

    /**
     * Set e1
     *
     * @param \DateTime $e1
     *
     * @return Report
     */
    public function setE1($e1)
    {
        $this->e1 = $e1;

        return $this;
    }

    /**
     * Get e1
     *
     * @return \DateTime
     */
    public function getE1()
    {
        return $this->e1;
    }

    /**
     * Set e2
     *
     * @param \DateTime $e2
     *
     * @return Report
     */
    public function setE2($e2)
    {
        $this->e2 = $e2;

        return $this;
    }

    /**
     * Get e2
     *
     * @return \DateTime
     */
    public function getE2()
    {
        return $this->e2;
    }

    /**
     * Set e3
     *
     * @param \DateTime $e3
     *
     * @return Report
     */
    public function setE3($e3)
    {
        $this->e3 = $e3;

        return $this;
    }

    /**
     * Get e3
     *
     * @return \DateTime
     */
    public function getE3()
    {
        return $this->e3;
    }
}
