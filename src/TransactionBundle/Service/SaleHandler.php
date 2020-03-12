<?php

namespace TransactionBundle\Service;
use TransactionBundle\Entity\STransaction;
use TransactionBundle\Entity\Sale;
use KmBundle\Entity\Branch;
use UserBundle\Entity\User;

//use FOS\RestBundle\View\View;

class SaleHandler
{
    //To store the entity manager
    private $em;
    private $stockHandler;
    private $tokenStorage;
    
    public function __construct($em, $stockHandler, $tokenStorage) 
    {
        $this->em = $em;
        $this->stockHandler = $stockHandler;
        $this->tokenStorage = $tokenStorage;
    }
    
    public function processSaleTransaction(array $inputData, Branch $branch)
    {
        //Create an instance of a SaleTransaction & hydrate it with the branch and the total amount of the transaction
        $stransaction = new STransaction();
        $stransaction->setTotalAmount($inputData['total']);
        $stransaction->setBranch($branch);
        //Link the employee to the transaction
        $user = $this->tokenStorage->getToken()->getUser();
        $stransaction->setUser($user);
        
        //Loop over each sale
        foreach ($inputData['order'] as $s){
            //create an instance of a sale
            $sale = new Sale();
            //Link the sale to the related product
            $product = $this->em->getRepository('TransactionBundle:Product')->find($s['item']['id']);
            $sale->setProduct($product);
            //Call the stocktHandler service to update the stock
            $this->stockHandler->updateStock($branch, $product, $s['orderedItemCnt'], true);
            //Set the quantity
            $sale->setQuantity($s['orderedItemCnt']);
            $sale->setAmount($s['totalPrice']);
            $sale->setProfit();
            $sale->setStransaction($stransaction);
            $this->em->persist($sale);
        }
        //Generate the idSynchrone as the transaction has been initiate on the server itself.
        $stransaction->setIdSynchrone(null);
        //Persist its in DB.
        $this->em->persist($stransaction);
        $this->em->flush();
    }
    
    
    public function processSaleTransaction2(array $inputData, Branch $branch, User $user)
    {
        //Create an instance of a SaleTransaction & hydrate it with the branch and the total amount of the transaction
        $stransaction = new STransaction();
        //Keep the datime from the client. The same dateTime will be used for each sale as well
        $dateTime = new \DateTime($inputData['date_time']);
        $stransaction->setCreatedAt($dateTime);
        $stransaction->setTotalAmount($inputData['total']);
        $stransaction->setBranch($branch);
        //set the idSynchrone sent by the client
        $stransaction->setIdSynchrone($inputData['st_synchrone_id']);
        //Link the employee to the transaction
        $stransaction->setUser($user);

        //Loop over each sale
        foreach ($inputData['order'] as $s){
            //create an instance of a sale
            $sale = new Sale();
            //Link the sale to the related product
            $product = $this->em->getRepository('TransactionBundle:Product')->find($s['id']);
            $sale->setProduct($product);
            //Keep DateTime from client
            $sale->setCreatedAt($dateTime);
            //Set the quantity
            $sale->setQuantity($s['orderedItemCnt']);
            $sale->setProfit();
            //Call the stocktHandler service to update the stock
            $this->stockHandler->updateStock($branch, $product, $s['orderedItemCnt'], true);
            $sale->setAmount($s['totalPrice']);
            $sale->setStransaction($stransaction);
            $this->em->persist($sale);
        }
        //Persist its in DB.
        $this->em->persist($stransaction);
        $this->em->flush();
    }
}
