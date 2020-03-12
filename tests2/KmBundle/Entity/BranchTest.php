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
    
    public function testGetAlertStocks()
    {
        //Notice that one of the stock is decreasing by a script any time test is running
        //Get the branch
        $branch = $this->em->getRepository('KmBundle:Branch')->find(3);
        //it have to be BATA
        $this->assertEquals($branch->getName(), 'BATA');
        //Get all the alertStocks from this branch
        $alertStocks = $branch->getAlertStocks();
        $this->assertEquals(count($alertStocks), 4);
        $this->assertEquals($alertStocks[0]->getName(), 'JUS TOP 1.5 L_B');
    }
}
