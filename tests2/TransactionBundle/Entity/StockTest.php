<?php

/*
 * This file is part of Components of KingManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) TinzapaTech <info@yamegroup.com>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\TransactionBundle\Service;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StockTest extends WebTestCase
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
    
    public function testIncreaseStock()
    {
        //Get a stock from the fixtures
        $stock = $this->em->getRepository('TransactionBundle:Stock')->find(2);
        //Check the identity of the stock
        $this->assertEquals($stock->getName(), 'JUS TOP 1.5 L_B');
        //Get the initial value
        $stockInitValue = $stock->getValue();
        $this->assertEquals($stockInitValue, 5);
        
        //case of 1 incrementation
        $stock->decreaseValue(1);
        $this->assertEquals($stock->getValue(), 4);
        //Don't forget to initialize the value before test again
        $stock->setValue($stockInitValue);
        
        //case of 1 incrementation
        $stock->decreaseValue(2);
        $this->assertEquals($stock->getValue(), 3);
        //Don't forget to initialize the value before test again
        $stock->setValue($stockInitValue);
        
        //case of 1 incrementation
        $stock->decreaseValue(3);
        $this->assertEquals($stock->getValue(), 2);
        //Don't forget to initialize the value before test again
        $stock->setValue($stockInitValue);
        
        //case of 1 incrementation
        $stock->decreaseValue(5);
        $this->assertEquals($stock->getValue(), 0);
        //Don't forget to initialize the value before test again
        $stock->setValue($stockInitValue);
        
        //case of 1 incrementation
        $stock->decreaseValue(6);
        $this->assertEquals($stock->getValue(), -1);
        //Don't forget to initialize the value before test again
        $stock->setValue($stockInitValue);
        
        //case of 1 incrementation
        $stock->decreaseValue(100);
        $this->assertEquals($stock->getValue(), -95);
        //Don't forget to initialize the value before test again
        $stock->setValue($stockInitValue);
        
    }
    
    public function testIsAlertStock()
    {
        $stock2 = $this->em->getRepository('TransactionBundle:Stock')->find(2);
        //Make sure we are targetting the right stock from the fixture
        $this->assertEquals($stock2->getValue(), 5);
        $this->assertEquals($stock2->getName(), 'JUS TOP 1.5 L_B');
        $this->assertTrue($stock2->isAlertStock());
        
        $stock2 = $this->em->getRepository('TransactionBundle:Stock')->find(5);
        //Make sure we are targetting the right stock from the fixture
        $this->assertEquals($stock2->getValue(), -1);
        $this->assertEquals($stock2->getName(), 'CLE USB 4 GB_B');
        $this->assertTrue($stock2->isAlertStock());
        
    }
}
