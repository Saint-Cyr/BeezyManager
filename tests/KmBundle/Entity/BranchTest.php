<?php

/*
 * This file is part of Components of KingManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) TinzapaTech <mapoukacyr@yahoo.com>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\TransactionBundle\Service;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BranchTest extends WebTestCase
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
    
    /*
     * this test make sure any fly data returned by any method return
     * 0 if apply because up to the data of writing this test the methods
     */
    public function testGetFlyData()
    {
        $branch = $this->em->getRepository('KmBundle:Branch')->find(3);
        
        $this->assertEquals($branch->getFlySaleAmount(), 0);
    }
    
    /*public function testGetAlertStocks()
    {
        //Notice that one of the stock is decreasing by a script any time test is running
        //Get the branch
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        //it have to be BATA
        $this->assertEquals($branch->getName(), 'BATA');
        //Get all the alertStocks from this branch
        $alertStocks = $branch->getAlertStocks();
        $this->assertEquals(count($alertStocks), 2);
        //Check fixture data
        $this->assertEquals($alertStocks[0]->getProduct()->getName(), 'DVD');
        $this->assertEquals($alertStocks[0]->getAlertLevel(), 1);
        $this->assertEquals($alertStocks[0]->getValue(), 0);
        
        $this->assertEquals($alertStocks[1]->getProduct()->getName(), 'Manette 4500');
        $this->assertEquals($alertStocks[1]->getAlertLevel(), 2);
        $this->assertEquals($alertStocks[1]->getValue(), -1);
    }*/
}
