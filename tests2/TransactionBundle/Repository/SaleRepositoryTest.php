<?php

/*
 * This file is part of Components of KingManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) TinzapaTech <mapoukacyr@yahoo.fr>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\TransactionBundle\Service;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SaleRepositoryTest extends WebTestCase
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
        $this->reportHandler = $this->application->getKernel()->getContainer()->get('km.report_handler');
    }
    
    public function testGetSaleFromTo()
    {
        //Parameters
        $initialDate = new \DateTime('01-01-2008');
        $finalDate = new \DateTime('01-01-2009');
        $product = $this->em->getRepository('TransactionBundle:Product')->find(1);
        
        $sales = $this->em->getRepository('TransactionBundle:Sale')
                          ->getSaleFromTo($initialDate, $finalDate, $product);
        $this->assertEquals(count($sales), 2);
        
        foreach ($sales as $s){
            $this->assertEquals($s->getProduct()->getName(), 'Nexcare');
        }
    }
}
