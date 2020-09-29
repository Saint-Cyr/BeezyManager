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
            $sale->setProfit($s['saleProfit']);
            $sale->setStransaction($stransaction);
            $this->em->persist($sale);
        }
        //Persist its in DB.
        $this->em->persist($stransaction);
        $this->em->flush();
    }
    
    /*
     * This method aims to check the authencity of the Seller that have
     * made the STransaction from the BSol Client. It process is as follow:
     * 1: make sure the user related to the uploaded email exists
     * 2: Check and make sure the user is not desabled (isEnalbled() = true)
     * 3: @return array('response' => true, 'description' => '');
     */
    public function isSellerGenius($userEmail)
    {
        //Make sure the user related to the uploaded email does exist in DB.
        $user = $this->em->getRepository('UserBundle:User')->findOneBy(array('email' => $userEmail));
        if(!$user){
            return array('response' => false, 'description' => 'the user related to the email: '.$userEmail.' does not exist');
        }
        //Make sure the user is not locked neighether desible
        if($user->isAccountNonLocked() && $user->isEnabled()){
            return array('response' => TRUE, 'description' => '');
        }else{
            return $response = array('response' => false, 'description' => 'User Account related to the email '.$userEmail.' is locked or Desabled');
        }
    }
    
    /*
     * this method aims to check the validity of the Data Structure as follow
     * 1-check the integrety of the global data structure ($inputData)
     */
    public function isDataStructureValid($inputData)
    {
       //Make sure the $inputData array content 5 couples of key => value(s)
       if(count($inputData) != 6)
       {
           return array('response' => false, 
                        'description' => 'the main data structure must have 6 couple of keys => value(s) but it actually have '.count($inputData).'');
       }
       //Make sure each one of the required keys does exist
       if(!(array_key_exists('user_email', $inputData))){
           return array('response' => false, 'description' =>  'email key does not exist in the Data Structure');
       }
       if(!(array_key_exists('st_synchrone_id', $inputData))){
           return array('response' => false, 'description' =>  'st_synchrone_id key does not exist in the Data Structure');
       }
       if(!(array_key_exists('date_time', $inputData))){
           return array('response' => false, 'description' =>  'date_time key does not exist in the Data Structure');
       }
       if(!(array_key_exists('total', $inputData))){
           return array('response' => false, 'description' =>  'total key does not exist in the Data Structure');
       }
       if(!(array_key_exists('order', $inputData))){
           return array('response' => false, 'description' =>  'order key does not exist in the Data Structure');
       }
       if(!(array_key_exists('branch_online_id', $inputData))){
           return array('response' => false, 'description' =>  'branch_online_id key does not exist in the Data Structure');
       }
       //make sure the $order data structure content all of the 3 required keys
       if(count($inputData['order'][0]) != 4)
       {
           return array('response' => false, 'description' => 'the data structure $order must have 3 couples of keys => value(s)');
       }
           
       return array('response' => true, 'description' => '');
    }
    
    /*
     * this method aims to validate the branch to which the BSol Client has been
     * installed
     */
    public function isBranchValid($branchId)
    {
        //Check whether the branch exist in the server DB.
        $branchFromServer = $this->em->getRepository('KmBundle:Branch')->find($branchId);
        
        if(!$branchFromServer){
            return array('response' => false, 'description'=> 'branch related to the #ID '.$branchId.' not found from the server DB');
        }
        
        return array('response' => true, 'description'=> '');
        
    }
}
