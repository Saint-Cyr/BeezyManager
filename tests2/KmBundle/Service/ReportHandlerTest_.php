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
use TransactionBundle\Entity\Product;

class ReportHandlerTest extends WebTestCase
{
    private $em;
    private $application;
    private $reportHandler;


    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        
        $this->application = new Application(static::$kernel);
        $this->em = $this->application->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
        $this->reportHandler = $this->application->getKernel()->getContainer()->get('km.report_handler');
    }
    
    public function testGetReportA()
    {
        //Parameters
        $initialDate = new \DateTime('01-01-2008');
        $finalDate = new \DateTime('01-01-2009');
        
        $p1 = $this->em->getRepository('TransactionBundle:Product')->find(1);
        $p2 = $this->em->getRepository('TransactionBundle:Product')->find(2);
        
        $p3 = $this->em->getRepository('TransactionBundle:Product')->find(3);
        $p4 = $this->em->getRepository('TransactionBundle:Product')->find(4);
        
        $rproduct[] = $p1;
        $rproduct[] = $p2;
        
        $uproduct [] = $p3;
        $uproduct[] = $p4;
                
        $result1 = $this->reportHandler->getReportA($initialDate, $finalDate, $rproduct, $uproduct);
        $this->assertEquals(count($result1['other']), 2);
        
        //Parameters
        $initialDate2 = new \DateTime('01-01-2008');
        $finalDate2 = new \DateTime('01-01-2018');
        
        $pp1 = $this->em->getRepository('TransactionBundle:Product')->find(1);
        $pp2 = $this->em->getRepository('TransactionBundle:Product')->find(2);
        
        $pp3 = $this->em->getRepository('TransactionBundle:Product')->find(3);
        $pp4 = $this->em->getRepository('TransactionBundle:Product')->find(4);
        
        $rproduct2[] = $pp1;
        $rproduct2[] = $pp2;
        
        $uproduct2[] = $pp3;
        $uproduct2[] = $pp4;
                
        $result2 = $this->reportHandler->getReportA($initialDate2, $finalDate2, $rproduct2, $uproduct2);
        $this->assertEquals(count($result2['other']), 2);
        
    }
    
    public function testBuildReportAoneProduct()
    {
        //Parameters
        $initialDate = new \DateTime('01-01-2008');
        $finalDate = new \DateTime('01-01-2009');
        $product = $this->em->getRepository('TransactionBundle:Product')->find(1);
        $product = $this->reportHandler->buildReportAoneProduct($initialDate, $finalDate, $product);
        
        $this->assertEquals($product->getFlyProfit(), 246);
        $this->assertEquals($product->getFlyAmount(), 260);
    }
    
    public function testBuildReportAmanyProducts()
    {
        //Parameters (Case 1)
        $initialDate = new \DateTime('01-01-2008');
        $finalDate = new \DateTime('01-01-2009');
        $product1 = $this->em->getRepository('TransactionBundle:Product')->find(1);
        $product2 = $this->em->getRepository('TransactionBundle:Product')->find(2);
        
        $product1->setFlyAmount(null);
        $product2->setFlyAmount(null);
        
        $unreportedProduct [] = $product1;
        $unreportedProduct [] = $product2;
        
        //this is the set of some item for exple: Shop
        $shop = $this->reportHandler->buildReportAmanyProducts($initialDate, $finalDate, $unreportedProduct);
        $this->assertEquals($shop[0]->getName(), 'Nexcare');
        $this->assertEquals($shop[0]->getFlyAmount(), 260);
        
        $this->assertEquals($shop[1]->getName(), 'Oxford French Dictionary');
        $this->assertEquals($shop[1]->getFlyAmount(), 35);
        
        //Parameters (Case 2)
        $initialDate = new \DateTime('01-01-2010');
        $finalDate = new \DateTime('01-01-2010');
        
        $product1 = $this->em->getRepository('TransactionBundle:Product')->find(5);
        $this->assertEquals($product1->getName(), 'Cle USB 2 GB');
        $product1->setFlyAmount(null);
        
        $unreportedProduct2 [] = $product1;
        
        //this is the set of some item for exple: Shop
        $shop2 = $this->reportHandler->buildReportAmanyProducts($initialDate, $finalDate, $unreportedProduct2);
        $this->assertEquals($shop2[0]->getName(), 'Cle USB 2 GB');
        $this->assertEquals($shop2[0]->getFlyAmount(), 10000);
    }
    
    public function testRemoveOneProduct()
    {
        //List of product that must not be reported
        $p1 = new Product();
        $p2 = new Product();
        $p3 = new Product();
        $p3->setBarcode('1');
        
        $unreportedProducts = array($p1, $p2, $p3);
        $toBeRemoved = new Product;
        $toBeRemoved->setBarcode('1');
        $result1 = $this->reportHandler->removeOneProduct($unreportedProducts, $toBeRemoved);
        $this->assertEquals($result1, array($p1, $p2));
    }
    
    public function testRemoveManyProducts()
    {
        //List of product that must not be reported
        $p1 = new Product();
        $p2 = new Product();
        $p3 = new Product();
        
        $p1->setBarcode('1');
        $p2->setBarcode('2');
        $p3->setBarcode('3');
        
        //List of product that must not be reported
        $p4 = new Product();
        $p5 = new Product();
        $p6 = new Product();
        
        $p4->setBarcode('4');
        $p5->setBarcode('5');
        $p6->setBarcode('6');
        
        $unreportedProducts = array($p1, $p2, $p3, $p4, $p5, $p6);
        
        $productToBeUnreported = array($p4, $p5, $p6);
        
        $result1 = $this->reportHandler->removeManyProducts($unreportedProducts, $productToBeUnreported);
        $this->assertEquals($result1, array($p1, $p2, $p3));
    }
    
    public function testGetProfitOnFly()
    {
        $stransaction1 = $this->em->getRepository('TransactionBundle:STransaction')->findAll();
        $result1 = $this->reportHandler->getProfitOnFly($stransaction1);
        $this->assertEquals($result1, 5384.0);
        
        $stransaction1 = $this->em->getRepository('TransactionBundle:STransaction')->find(2);
        //$stransaction2 = $this->em->getRepository('TransactionBundle:STransaction')->find(3);
        //$stransaction3 = $this->em->getRepository('TransactionBundle:STransaction')->find(4);
        
        $stransactions [] = $stransaction1;
        //$stransactions [] = $stransaction2;
        //$stransactions [] = $stransaction3;
        
        $result1 = $this->reportHandler->getProfitOnFly($stransactions);
        $this->assertEquals($result1, 4000);
    }
    
    public function testGetSaleAmountOnFly()
    {
        $stransaction1 = $this->em->getRepository('TransactionBundle:STransaction')->find(2);
        //$stransaction2 = $this->em->getRepository('TransactionBundle:STransaction')->find(3);
        //$stransaction3 = $this->em->getRepository('TransactionBundle:STransaction')->find(4);
        
        $stransactions [] = $stransaction1;
        
        $result1 = $this->reportHandler->getSaleAmountOnFly($stransactions);
        $this->assertEquals($result1, 10000);
    }
}

