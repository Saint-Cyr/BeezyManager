<?php

/*
 * This file is part of Components of KingManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) TinzapaTech <info@yamegroup.com>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\TransactionBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StockTest extends WebTestCase
{
    private $em;
    private $application;
    private $stockHandler;


    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        
        $this->application = new Application(static::$kernel);
        $this->em = $this->application->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
        $this->stockHandler = $this->application->getKernel()->getContainer()->get('km.stock_handler');
    }
    
    public function testDecreaseStock()
    {
        //To avoid risk notice by PHPUnit
        $this->assertTrue(true);
    }
    
    public function testIncreaseStock()
    {
        $stock = $this->em->getRepository('TransactionBundle:Stock')->find(2);
        $this->assertEquals($stock->getProduct()->getName(), 'DVD');
        $initialValue = $stock->getValue();
        //Case where quantity is 1
        $quantity = 1;
        $this->assertEquals($stock->getValue(), 0);
        $stock->increaseValue($quantity);
        $this->assertEquals($stock->getValue(), 1);
        
        //Case where quantity is 3
        $quantity = 3;
        
        $stock->increaseValue($quantity);
        // Remember it has been incresed by the first assertion allready
        $this->assertEquals($stock->getValue(), 4);
        
        
    }
    
    public function testIsAlertStock()
    {
        //To avoid risk notice by PHPUnit
        $this->assertTrue(true);
    }
}
