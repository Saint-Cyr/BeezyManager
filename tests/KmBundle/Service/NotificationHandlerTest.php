<?php

/*
 * This file is part of Components of BeezyManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) Saint-Cyr <mapoukacyr@yahoo.fr>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\KmBundle\Service;

use UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationHandlerTest extends WebTestCase
{
    private $em;
    private $application;
    private $notificationHandler;


    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        
        $this->application = new Application(static::$kernel);
        $this->em = $this->application->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
        $this->notificationHandler = $this->application->getKernel()->getContainer()->get('km.notification_handler');
    }
    
    public function testAddSTransaction()
    {
        /*Simple add data to the database. In order to to do that:
         * 1- Remove the last record to clean the CanceledSTransaction entity
         * 2- add the CanceledSTransaction 
         * 3- get the current record number $currentRecordNb
         * 4- Make sure that: $initialRecord == $currentRecord
         */
        
        $user = $this->em->getRepository('UserBundle:User')->find(1);
        
        $canceledSTransaction = $this->em->getRepository('TransactionBundle:CanceledSTransaction')->findAll();
        
        foreach ($canceledSTransaction as $cST){
            $this->em->remove($cST);
            $this->em->flush();
        }
        
        $initialRecordNb = count($canceledSTransaction);
        //Add the CanceledSTransaction
        $stransaction = $this->em->getRepository('TransactionBundle:STransaction')->find(1);
        
        $this->notificationHandler->addSTransaction($stransaction, $user);
        
        $currentRecordNb = count($this->em->getRepository('TransactionBundle:CanceledSTransaction')->findAll());
        
        $this->assertEquals($initialRecordNb, $currentRecordNb);
        //Make sure the data structure is right
        // as we used array for CanceledSTransaction:sales in doctrine
        
        $cSTs = $this->em->getRepository('TransactionBundle:CanceledSTransaction')->findAll();
        foreach ($cSTs as $cST){
            $object = $cST;
        }
        
        //General properties can be accessed like this:
        $this->assertEquals($object->getTotalAmount(), 6300);
        $this->assertEquals($object->getCreatedAt(), new \DateTime("01-10-2014"));
        $this->assertEquals($object->getCanceledAt(), new \DateTime("now"));
        
        $sales = $object->getSales();
        //the sales of a STransaction can be reach that way
        $this->assertEquals($sales[1], array('quantity' => 1, 'name' => 'CD Simple'));
        $this->assertEquals($sales[2], array('quantity' => 1, 'name' => 'DVD'));
        
    }
}

