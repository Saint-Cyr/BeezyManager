<?php

/*
 * This file is part of Components of KingManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) TizampaTech <mapoukacyr@yahoo.fr>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */
namespace Tests\TransactionBundle\Service;

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
        $sale = $this->em->getRepository('TransactionBundle:Sale')->find(1);
        $this->assertEquals($sale->getProfit(), 123);
        
        $sale = $this->em->getRepository('TransactionBundle:Sale')->find(12);
        $this->assertEquals($sale->getProfit(), 4000);
        
        
    }
    
    public function testGetAmount()
    {
        $sale = $this->em->getRepository('TransactionBundle:Sale')->find(1);
        $this->assertEquals($sale->getAmount(), 130);
    }
}
