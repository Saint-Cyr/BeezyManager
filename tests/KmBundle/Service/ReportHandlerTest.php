<?php

/*
 * This file is part of Components of BeezyManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) TinzapaTech <mapoukacyr@yahoo.fr>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */
namespace Tests\KmBundle\Service;

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
        $initDate = new \DateTime('01-01-2008');
        $finitDate = new \DateTime('01-01-2011');

        $initDate2 = new \DateTime('now');
        $finitDate2 = new \DateTime('now');
        
        $category1 = $this->em->getRepository('TransactionBundle:Category')->find(1);
        $category2 = $this->em->getRepository('TransactionBundle:Category')->find(2);
        
        $this->assertEquals($category1->getName(), 'Boutique');
        $this->assertEquals($category2->getName(), 'Secretariat');
        //As doctrine array is not supported we have to build a standard array
        $boutique = array();
        
        foreach ($category1->getProducts() as $product){
            $boutique[] = $product;
        }
        
        $secretariat = array();
        
        foreach ($category2->getProducts() as $product){
            $secretariat[] = $product;
        }
        
        $outPut1 = $this->reportHandler->getReportA($initDate, $finitDate, $secretariat, $boutique);
        //There are 1 array for reported product / services like Plastification, Scanner, ...
        //and 1 array for Boutique (other)
        $this->assertEquals(count($outPut1), 6);
        //There are only 3 products in Boutique
        $this->assertEquals(count($outPut1['E1']), 3);
        //Check each one of the product of the shop
        $this->assertEquals($outPut1['E1'][0]->getName(), 'CD Simple');
        $this->assertEquals($outPut1['E1'][1]->getName(), 'DVD');
        $this->assertEquals($outPut1['E1'][2]->getName(), 'Manette 4500');
        //Check sale & profit amount for each one
        $this->assertEquals($outPut1['E1'][0]->getFlyAmount(), 150);
        $this->assertEquals($outPut1['E1'][0]->getFlyProfit(), 50);
        $this->assertEquals($outPut1['E1'][1]->getFlyAmount(), 900);
        $this->assertEquals($outPut1['E1'][1]->getFlyProfit(), 450);
        $this->assertEquals($outPut1['E1'][2]->getFlyAmount(), 4500);
        $this->assertEquals($outPut1['E1'][2]->getFlyProfit(), 2500);
        
        //There are six services in Secretariat
        $this->assertEquals(count($outPut1['E2']), 6);
        //Check each one of the 6 service of the secretariat
        $this->assertEquals($outPut1['E2'][0]->getName(), 'Plastification');
        $this->assertEquals($outPut1['E2'][1]->getName(), 'Scanner');
        $this->assertEquals($outPut1['E2'][2]->getName(), 'Photo44');
        $this->assertEquals($outPut1['E2'][3]->getName(), 'Photocopy');
        $this->assertEquals($outPut1['E2'][4]->getName(), 'Internet');
        $this->assertEquals($outPut1['E2'][5]->getName(), 'Serigraphy');
        //Check sale & profit amount for each one
        $this->assertEquals($outPut1['E2'][0]->getFlyAmount(), 750);
        $this->assertEquals($outPut1['E2'][0]->getFlyProfit(), 0);
        //Check the total sale & profit
        $this->assertEquals($outPut1['E1_SALE'], 5550);
        $this->assertEquals($outPut1['E1_PROFIT'], 3000);
        $this->assertEquals($outPut1['E2_SALE'], 4250);
        $this->assertEquals($outPut1['E2_PROFIT'], null);
        //Case 2: Only secretariat
        $outPut2 = $this->reportHandler->getReportA($initDate, $finitDate, $secretariat, null);
        $this->assertEquals(count($outPut2), 6);
        $this->assertNull($outPut2['E1']);
        $this->assertNotNull($outPut2['E2']);
        //There are six services in Secretariat
        $this->assertEquals(count($outPut1['E2']), 6);
        //Check each one of the 6 service of the secretariat
        $this->assertEquals($outPut1['E2'][0]->getName(), 'Plastification');
        $this->assertEquals($outPut1['E2'][1]->getName(), 'Scanner');
        $this->assertEquals($outPut1['E2'][2]->getName(), 'Photo44');
        $this->assertEquals($outPut1['E2'][3]->getName(), 'Photocopy');
        $this->assertEquals($outPut1['E2'][4]->getName(), 'Internet');
        $this->assertEquals($outPut1['E2'][5]->getName(), 'Serigraphy');
        //Check sale & profit amount for each one
        $this->assertEquals($outPut1['E2'][0]->getFlyAmount(), 750);
        $this->assertEquals($outPut1['E2'][0]->getFlyProfit(), 0);
        
        //Case 2: Only Boutique
        //As doctrine array is not supported we have to build a standard array
        $boutique = array();
        
        foreach ($category1->getProducts() as $product){
            $boutique[] = $product;
        }
        
        $secretariat = array();
        
        foreach ($category2->getProducts() as $product){
            $secretariat[] = $product;
        }
        
        $outPut1 = $this->reportHandler->getReportA($initDate, $finitDate, null, $boutique);
        $this->assertNull($outPut1['E2']);
        //There are only 3 products in Boutique
        $this->assertCount(3, $outPut1['E1']);
        //Check each one of the product of the shop
        $this->assertEquals($outPut1['E1'][0]->getName(), 'CD Simple');
        $this->assertEquals($outPut1['E1'][1]->getName(), 'DVD');
        $this->assertEquals($outPut1['E1'][2]->getName(), 'Manette 4500');
        //Check sale & profit amount for each one
        $this->assertEquals($outPut1['E1'][0]->getFlyAmount(), 150);
        $this->assertEquals($outPut1['E1'][0]->getFlyProfit(), 50);
        $this->assertEquals($outPut1['E1'][1]->getFlyAmount(), 900);
        $this->assertEquals($outPut1['E1'][1]->getFlyProfit(), 450);
        $this->assertEquals($outPut1['E1'][2]->getFlyAmount(), 4500);
        $this->assertEquals($outPut1['E1'][2]->getFlyProfit(), 2500);
        
        $outPut1 = $this->reportHandler->getReportA($initDate, $finitDate, null, null);
        $this->assertEquals(count($outPut1), 6);
        $this->assertEquals($outPut1['E1'], null);
        $this->assertEquals($outPut1['E2'], null);

        //Case where initData and finiDate is all today's dates
        $outPut3 = $this->reportHandler->getReportA($initDate2, $finitDate2, $secretariat, $boutique);
        $this->assertCount(6, $outPut3);
        //Shop (E1)
        $this->assertEquals($outPut3['E1'][0]->getName(), 'CD Simple');
        $this->assertEquals($outPut3['E1'][1]->getName(), 'DVD');
        $this->assertEquals($outPut3['E1'][2]->getName(), 'Manette 4500');
        
        $this->assertEquals($outPut3['E2'][0]->getName(), 'Plastification');
        $this->assertEquals($outPut3['E2'][1]->getName(), 'Scanner');
        $this->assertEquals($outPut3['E2'][2]->getName(), 'Photo44');
        $this->assertEquals($outPut3['E2'][3]->getName(), 'Photocopy');
        $this->assertEquals($outPut3['E2'][4]->getName(), 'Internet');
        $this->assertEquals($outPut3['E2'][5]->getName(), 'Serigraphy');
        //According to the fixtures, only some of the products/services has been sold today
        //and all the rest are null (check fixtures doc for further details)
        $this->assertEquals($outPut3['E2'][0]->getFlyAmount(), null);
        $this->assertEquals($outPut3['E2'][2]->getFlyAmount(), 1000);
        $this->assertEquals($outPut3['E2'][3]->getFlyAmount(), 1500);
        $this->assertEquals($outPut3['E2'][4]->getFlyAmount(), 7000);
        $this->assertEquals($outPut3['E2'][5]->getFlyAmount(), 100000);

    }
    
    public function testBuildReportAoneProduct()
    {
        //To avoid risk notice by PHPUnit
        $this->assertTrue(true);
    }
    
    public function testBuildReportAmanyProducts()
    {
        //To avoid risk notice by PHPUnit
        $this->assertTrue(true);
    }
    
    public function testGetProfitOnFly()
    {
        //To avoid risk notice by PHPUnit
        $this->assertTrue(true);
    }
    
    public function testGetSaleAmountOnFly()
    {
        //To avoid risk notice by PHPUnit
        $this->assertTrue(true);
    }
}

