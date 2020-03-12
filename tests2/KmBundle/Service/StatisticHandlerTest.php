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

class StatisticHandlerTest extends WebTestCase
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
    public function testGetSaleByMonth()
    {
        /*
        //Case 1: January is 100
        $outPut1 = $this->statisticHandler->getSaleByMonth(16);
        $this->assertEquals($outPut1['jan'], 100);
        //Case 2: Febrary is 200
        $this->assertEquals($outPut1['feb'], 200);
        //Case 2: March is 300
        $this->assertEquals($outPut1['mar'], 300);
        //Case 2: April is 400
        $this->assertEquals($outPut1['apr'], 400);
        //Case 2: May is 500
        $this->assertEquals($outPut1['may'], 500);
        //Case 2: May is 600
        $this->assertEquals($outPut1['jun'], 600);
        //Case 2: Febrary is 700
        $this->assertEquals($outPut1['jul'], 700);
        //Case 2: Febrary is 800
        //$this->assertEquals($outPut1['aug'], 800);
        //Case 2: Febrary is 900
        //$this->assertEquals($outPut1['sep'], 900);
        //Case 2: Febrary is 1000
        $this->assertEquals($outPut1['oct'], 1000);
        //Case 2: Febrary is 1100
        $this->assertEquals($outPut1['nov'], 1230);
        //Case 2: Febrary is 1200
        $this->assertEquals($outPut1['dec'], 1200);
        
        $outPut2 = $this->statisticHandler->getSaleByMonth(15);
        $this->assertEquals($outPut2['jan'], 1300);
        $this->assertEquals($outPut2['feb'], 0);
         $this->assertEquals($outPut2['mar'], 0);*/
        
    }


    public function testGetSale()
    {
        //Case 1: get sale for all branches. (It have to be 1000.50 based on fixtures)
        $outPut1 = $this->statisticHandler->getSale(null, null);
        //$this->assertEquals($outPut1, 10700.50);
        
        //Case 2: get sale for a branch
        //...
    }
    
    /*
     * To be review
     */
    public function testGetProfit()
    {
        //Case 1: get profit for all branches (It have to be 50.30 based on fixtures)
        $outPut1 = $this->statisticHandler->getProfit(null, null);
        //$this->assertEquals($outPut1, 1230.0);
    }
    
    public function testGetExpenditure()
    {
        //Case 1: get all expenditure
        $outPut1 = $this->statisticHandler->getExpenditure();
        //$this->assertEquals($outPut1, 80.20);
    }
    
    public function testGetBalance()
    {
        //Case 1: balance is positive
        $outPut1 = $this->statisticHandler->getBalance();
        //$this->assertEquals($outPut1, 1149.80);
    }
    
    public function testGetSaleByBranch()
    {
        //Get the branch
        $branch = $this->em->getRepository('KmBundle:Branch')->find(2);
        $outPut1 = $this->statisticHandler->getSaleByBranch($branch);
        //$this->assertEquals($outPut1, 9800);
    }
    
    public function testGetProfitByBranch()
    {
        //Case 1: test the profit of @branch1
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        $outPut1 = $this->statisticHandler->getProfitByBranch($branch);
        //$this->assertEquals($outPut1, 1230.0);
        
        //Case 2: test the profit of @branch2
        $branch = $this->em->getRepository('KmBundle:Branch')->find(2);
        $outPut1 = $this->statisticHandler->getProfitByBranch($branch);
        //$this->assertEquals($outPut1, 0.00);
    }
    
    public function testGetExpenditureByBranch()
    {
        //Case 1: test the expenditure value related to @branch1 
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        $outPut1 = $this->statisticHandler->getExpenditureByBranch($branch);
        //$this->assertEquals($outPut1, 80.20);
        
        //Case 2: test the expenditure value related to @branch2 
        $branch = $this->em->getRepository('KmBundle:Branch')->find(2);
        $outPut1 = $this->statisticHandler->getExpenditureByBranch($branch);
        //$this->assertEquals($outPut1, 0.00);
    }
    
    public function testGetBalanceByBranch()
    {
        //Case 1: balance is positive (for @branch1)
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        $outPut1 = $this->statisticHandler->getBalanceByBranch($branch);
        //$this->assertEquals($outPut1, 1149.80);
        
        //Case 2: balance is null (for @branch2)
        $branch = $this->em->getRepository('KmBundle:Branch')->find(2);
        $outPut1 = $this->statisticHandler->getBalanceByBranch($branch);
        //$this->assertEquals($outPut1, 0.00);
    }
}

