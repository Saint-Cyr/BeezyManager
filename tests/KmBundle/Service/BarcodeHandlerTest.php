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

class BarcodeHandlerTest extends WebTestCase
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
    
    public function testGenerateBarcode()
    {
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        $this->assertEquals($branch->getName(), 'BATA');
        //Load all the stock
        $stocks = $this->em->getRepository('TransactionBundle:Stock')->getTrackedByBranch($branch, true);
        foreach ($stocks as $s){
            $products[] = $s->getProduct();
        }
        
        $this->assertEquals(count($stocks), 4);
        $this->assertEquals(count($products), 4);
    }
}