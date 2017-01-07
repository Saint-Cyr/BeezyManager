<?php

namespace KmBundle\Service;

use KmBundle\Entity\Branch;
use TransactionBundle\Entity\Product;
use TransactionBundle\Entity\Stock;

class StockHandler
{
    //To store the entity manager
    private $em;
    
    public function __construct($em) 
    {
        $this->em = $em;
    }
    
    /**
     * @param Branch | Product
     */
    public function updateStock(Branch $branch, Product $product, $quantity)
    {
        foreach ($product->getStocks() as $stock){
            
            if($stock->getBranch()->getId() == $branch->getId()){
          
                $stock->decreaseValue($quantity);
                //Persist the change in the Database
                //Update the property alertStockCreatedAt if there is alert
                $stock->isAlertStock();
                
                $this->em->persist($stock);
                $this->em->flush();
            }
        }
    }
}
