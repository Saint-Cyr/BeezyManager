<?php

/*
 * This file is part of Components of BeezyManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) TinzapaTech <mapoukacyr@yahoo.fr>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\TransactionBundle\Service;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StockHandlerTest extends WebTestCase
{
    private $em;
    private $application;
    private $statisticHandler;


    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        
        $this->application = new Application(static::$kernel);
        $this->em = $this->application->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
        $this->statisticHandler = $this->application->getKernel()->getContainer()->get('km.statistic_handler');
    }
    
    /*
     * This have to be updated
     */
    public function testUpdateStock()
    {
        //Get the product and the stock
        $product = $this->em->getRepository('TransactionBundle:Product')->find(3);
        //it have to be Jus Top 0.35 L and it related stock
        $this->assertEquals($product->getName(), 'Jus Top 0.35 L');
        //Get the branch
        $branch = $this->em->getRepository('KmBundle:Branch')->find(3);
        //it have to be BATA
        $this->assertEquals($branch->getName(), 'BATA');
        $stocks = $product->getStocks();
        //Get the stockHandler service
        $stockHandler = $this->application->getKernel()->getContainer()->get('km.stock_handler');
        
        //Case where the quantity of the product is 1
        $quantity = 1;
        //Get the initial value of the stock in order to test the change made by the stockHandler service
        $stock = $this->em->getRepository('TransactionBundle:Stock')->find(1);
        $stockInitValue  = $stock->getValue();
        $stockHandler->updateStock($branch, $product, $quantity);
        $this->assertEquals($stock->getValue(), ($stockInitValue - $quantity));
        
        //Case where the quantity of the product is 2
        $quantity = 2;
        $stockInitValue  = $stock->getValue();
        $stockHandler->updateStock($branch, $product, $quantity);
        $this->assertEquals($stock->getValue(), ($stockInitValue - $quantity));
    }
}

