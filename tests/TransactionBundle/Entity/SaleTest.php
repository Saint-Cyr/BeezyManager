<?php

/*
 * This file is part of Components of KingManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) TizampaTech <mapoukacyr@yahoo.fr>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */
namespace Tests\TransactionBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SaleTest extends WebTestCase
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
    
    public function testGetProfit()
    {
        $this->assertTrue(true);
        $sale = $this->em->getRepository('TransactionBundle:Sale')->find(1);
        $this->assertEquals($sale->getProfit(), 50);
        
        $sale = $this->em->getRepository('TransactionBundle:Sale')->find(2);
        $this->assertEquals($sale->getProfit(), 150);
        
        $sale = $this->em->getRepository('TransactionBundle:Sale')->find(3);
        $this->assertEquals($sale->getProfit(), 300);
        
        $sale = $this->em->getRepository('TransactionBundle:Sale')->find(12);
        $this->assertEquals($sale->getProfit(), null);
        
    }
    
    public function testGetAmount()
    {
        $sale1 = $this->em->getRepository('TransactionBundle:Sale')->find(1);
        $this->assertEquals($sale1->getAmount(), 150);
        
        $sale2 = $this->em->getRepository('TransactionBundle:Sale')->find(2);
        $this->assertEquals($sale2->getAmount(), 300);
        
        $sale3 = $this->em->getRepository('TransactionBundle:Sale')->find(3);
        $this->assertEquals($sale3->getAmount(), 600);
        
        $sale4 = $this->em->getRepository('TransactionBundle:Sale')->find(4);
        $this->assertEquals($sale4->getAmount(), 4500);
        
        $sale5 = $this->em->getRepository('TransactionBundle:Sale')->find(5);
        $this->assertEquals($sale5->getAmount(), 250);
    }
    
    public function testSetAmount()
    {
        $this->assertTrue(true);
        $sale = $this->em->getRepository('TransactionBundle:Sale')->find(1);
        $sale->setAmount(100);
        $this->assertEquals(100, $sale->getAmount());
        
        $sale->setAmount();
        $this->assertEquals(150, $sale->getAmount());
    }
    
    /*
     * Important update of 14 July 2018 (requested by Mr Muhamed from
     * New World Telecom in Cameroon)
     * this make it possible to set discount on every sale
     * unit of STransaction
     * for Example if someone by 2 laptops that cost 120 000 FCA and 150 000 FCFA while
     * in the system, each one have the same unit price wich one is 150 000 FCFA then
     * it is now possible to just set discount on that particular sale in order
     * to have it amount ($sale->getAmount()) as 270 000 instead of 300 000
     */
    public function testSetAmountWithDiscount()
    {
        
    }
}
