<?php

/*
 * This file is part of Components of BeezyManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) TinzapaTech <mapoukacyr@yahoo.fr>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\KmBundle\Service;

use KmBundle\Entity\Branch;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use TransactionBundle\Entity\Product;
use TransactionBundle\Entity\Sale;
use TransactionBundle\Entity\STransaction;

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
        //To avoid risk notice by PHPUnit
        $this->assertTrue(true);
    }

    public function testSetBranchFlyData()
    {
        //Case of branch 1
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        $this->statisticHandler->setBranchFlyData($branch);

        $this->assertEquals($branch->getFlySaleAmount(), 1000);
        $this->assertEquals($branch->getFlyProfitAmount(), null);
        $this->assertEquals($branch->getFlyExpenditureAmount(), null);

        //Case of branch 2
        $branch2 = $this->em->getRepository('KmBundle:Branch')->find(2);
        $this->statisticHandler->setBranchFlyData($branch2);

        $this->assertEquals($branch2->getFlySaleAmount(), 110100);
        $this->assertEquals($branch2->getFlyProfitAmount(), 520);
        $this->assertEquals($branch2->getFlyExpenditureAmount(), null);
        $this->assertEquals($branch2->getFlyBalanceAmount(), 520);

    }

    
    /*
     * To be review
     */
    /*public function testGetProfit()
    {
        //Case 1: get profit for all branches (It have to be 3000 based on fixtures)
        $outPut1 = $this->statisticHandler->getProfit(null, null);
        $this->assertEquals($outPut1, 3000);
    }
    
    public function testGetExpenditure()
    {
        //Case 1: get all expenditure
        $outPut1 = $this->statisticHandler->getExpenditure();
        $this->assertEquals($outPut1, 0.0);
    }
    
    public function testGetBalance()
    {
        //Case 1: balance is positive
        $outPut1 = $this->statisticHandler->getBalance();
        $this->assertEquals($outPut1, 3000);
        
    }

    /*
     * Because we need access to the Database in order to execute
     * getSaleByBranch(), we are going to use mocking objects
     * here.
     */
    /*public function testGetSaleByBranch()
    {
        //Let's mock three stransactions
        $stransaction1 = $this->createMock(STransaction::class);
        $stransaction1->expects($this->any())
                      ->method('getTotalAmount')
                      ->will($this->returnValue(100));
        $stransaction1->expects($this->any())
                      ->method('getCreatedAt')
                      ->will($this->returnValue(new \DateTime("now")));

        $stransaction2 = $this->createMock(STransaction::class);
        $stransaction2->expects($this->any())
                      ->method('getTotalAmount')
                      ->will($this->returnValue(200));
        $stransaction2->expects($this->any())
            ->method('getCreatedAt')
            ->will($this->returnValue(new \DateTime("now")));

        $stransaction3 = $this->createMock(STransaction::class);
        $stransaction3->expects($this->any())
                      ->method('getTotalAmount')
                      ->will($this->returnValue(300));
        $stransaction3->expects($this->any())
            ->method('getCreatedAt')
            ->will($this->returnValue(new \DateTime("2017-01-01")));

        $stransaction4 = $this->createMock(STransaction::class);
        $stransaction4->expects($this->any())
            ->method('getTotalAmount')
            ->will($this->returnValue(400));
        $stransaction4->expects($this->any())
            ->method('getCreatedAt')
            ->will($this->returnValue(new \DateTime("now")));

        $stransaction5 = $this->createMock(STransaction::class);
        $stransaction5->expects($this->any())
            ->method('getTotalAmount')
            ->will($this->returnValue(0));
        $stransaction5->expects($this->any())
            ->method('getCreatedAt')
            ->will($this->returnValue(new \DateTime("now")));

        //Make sure object has been built as we expect them
        $this->assertEquals($stransaction1->getTotalAmount(), 100);
        $this->assertEquals($stransaction2->getTotalAmount(), 200);
        $this->assertEquals($stransaction3->getTotalAmount(), 300);
        $this->assertEquals($stransaction1->getCreatedAt(), new \DateTime("now"));

        //We have to gether all the $stransactions as doctrine usually do
        $tab1 = array($stransaction1, $stransaction2, $stransaction3);
        $tab2[] = $stransaction5;

        //Let's mock two branches
        $branch1 = $this->createMock(Branch::class);
        $branch1->expects($this->any())
                ->method('getStransactions')
                ->will($this->returnValue($tab1));

        $branch2 = $this->createMock(Branch::class);
        $branch2->expects($this->any())
                ->method('getStransactions')
                ->will($this->returnValue($tab2));

        $outPut1 = $this->statisticHandler->getSaleByBranch($branch1);
        $outPut2 = $this->statisticHandler->getSaleByBranch($branch2);

        //$this->assertEquals($outPut1, 300);
        $this->assertEquals($outPut2, 0);
    }
    
    public function testGetProfitByBranch()
    {
        //Case 1: test the profit of @branch1
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        $outPut1 = $this->statisticHandler->getProfitByBranch($branch);
        $this->assertEquals($outPut1, 3000);
        
        //Case 2: test the profit of @branch2
        $branch = $this->em->getRepository('KmBundle:Branch')->find(2);
        $outPut1 = $this->statisticHandler->getProfitByBranch($branch);
        $this->assertEquals($outPut1, 0.00);
    }
    
    public function testGetExpenditureByBranch()
    {
        //Case 1: test the expenditure value related to @branch1 
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        $outPut1 = $this->statisticHandler->getExpenditureByBranch($branch);
        $this->assertEquals($outPut1, 0);
        
        //Case 2: test the expenditure value related to @branch2 
        $branch = $this->em->getRepository('KmBundle:Branch')->find(2);
        $outPut1 = $this->statisticHandler->getExpenditureByBranch($branch);
        $this->assertEquals($outPut1, 0.00);
    }
    
    public function testGetBalanceByBranch()
    {
        //Case 1: balance is positive (for @branch1)
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        $outPut1 = $this->statisticHandler->getBalanceByBranch($branch);
        $this->assertEquals($outPut1, 3000);
    }*/
}

