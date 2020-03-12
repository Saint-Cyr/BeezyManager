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

class StockRepositoryTest extends WebTestCase
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
    
    public function testGetTrackedByBranch()
    {
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        $stocks = $this->em->getRepository('TransactionBundle:Stock')->getTrackedByBranch($branch, true);
        
        $this->assertCount(4, $stocks);
        $this->assertEquals($stocks[0]->getProduct()->getName(), 'CD Simple');
        $this->assertEquals($stocks[0]->getBranch()->getName(), 'BATA');
        
        $this->assertEquals($stocks[1]->getProduct()->getName(), 'DVD');
        $this->assertEquals($stocks[1]->getBranch()->getName(), 'BATA');
        
        $this->assertEquals($stocks[2]->getProduct()->getName(), 'Manette 4500');
        $this->assertEquals($stocks[2]->getBranch()->getName(), 'BATA');
        
        $this->assertEquals($stocks[3]->getProduct()->getName(), 'Chemise cartonier');
        $this->assertEquals($stocks[3]->getBranch()->getName(), 'BATA');
    }
    
    public function testGetStocked()
    {
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        $stocked = $this->em->getRepository('TransactionBundle:Stock')->getStocked($branch);
        $this->assertEquals(count($stocked), 1);
    }
    
    public function testGetDestocked()
    {
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        $destocked = $this->em->getRepository('TransactionBundle:Stock')->getDestocked($branch);
        $this->assertEquals(count($destocked), 1);
    }
    
}
