<?php

namespace KmBundle\Service;

use KmBundle\Entity\Branch;
use TransactionBundle\Entity\Product;
use TransactionBundle\Entity\Stock;
use TransactionBundle\Entity\CanceledSTransaction;
use \TransactionBundle\Entity\STransaction;
use UserBundle\Entity\User;

class NotificationHandler
{
    //To store the entity manager
    private $em;
    
    public function __construct($em) 
    {
        $this->em = $em;
    }
    
    public function addSTransaction(STransaction $stransaction, User $user)
    {
        //Prepare sales
        //$sales['branchId'] = $stransaction->getBranch()->getId();
        $sales[] = null;
        $tab[] = null;
        
        foreach ($stransaction->getSales() as $sale){
            $p = array('quantity' => $sale->getQuantity(), 'name' => $sale->getProduct()->getName());
            $sales[] = $p;
        }
        
        //create an hydrate the required model
        $canceledSTransaction = new CanceledSTransaction();
        $canceledSTransaction->setCreatedAt($stransaction->getCreatedAt());
        $canceledSTransaction->setCanceledAt(new \DateTime("now"));
        $canceledSTransaction->setTotalAmount($stransaction->getTotalAmount());
        $canceledSTransaction->setSales($sales);
        $canceledSTransaction->setBranchId($stransaction->getBranch()->getId());
        $canceledSTransaction->setOther($user->getName());
        
        
        $this->em->persist($canceledSTransaction);
        $this->em->flush();
    }
}
