<?php

/*
 * This file is part of Components of KingManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) YAME Group <info@yamegroup.com>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\TransactionBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class STransactionTest extends WebTestCase
{
    private $em;
    private $application;
    private $saleHandler;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        
        $this->application = new Application(static::$kernel);
        $this->em = $this->application->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
        $this->saleHandler = $this->application->getKernel()->getContainer()->get('transaction.sale_handler');
    }
    
    public function testGetIdSynchrone()
    {
        //To do : work with mocked object
        $stransaction = $this->em->getRepository('TransactionBundle:STransaction')->find(1);
        //set and idSynchrone intentionally
        $stransaction->setIdSynchrone('qwerty');
        //Make sure this idSynchrone has been set
        $this->assertEquals($stransaction->getIdSynchrone(), 'qwerty');
    }


    public function testGetProfit()
    {
        //Get a STransaction from the fixtures
        $STransaction = $this->em->getRepository('TransactionBundle:STransaction')->find(1);
        $this->assertEquals($STransaction->getProfit(), 3000.00);
        
        $STransaction = $this->em->getRepository('TransactionBundle:STransaction')->find(5);
        $this->assertEquals($STransaction->getProfit(), 520);
        
        $STransaction = $this->em->getRepository('TransactionBundle:STransaction')->find(2);
        $this->assertEquals($STransaction->getProfit(), null);
    }
    
    public function testGetTotalAmount()
    {
        //To avoid risk notice by PHPUnit
        $STransaction1 = $this->em->getRepository('TransactionBundle:STransaction')->find(1);
        $this->assertEquals($STransaction1->getTotalAmount(), 6300);
        
        $STransaction2 = $this->em->getRepository('TransactionBundle:STransaction')->find(2);
        $this->assertEquals($STransaction2->getTotalAmount(), 3500);
    }
    
}
