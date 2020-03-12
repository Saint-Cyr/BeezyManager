<?php

/*
 * This file is part of Components of BeezyManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) iSTech 2020 <mapoukacyr@yahoo.fr>
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

class SynchronizerHandlerTest extends WebTestCase
{
    private $em;
    private $application;
    private $synchronizerHandler;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        
        $this->application = new Application(static::$kernel);
        $this->em = $this->application->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
        $this->synchronizerHandler = $this->application->getKernel()->getContainer()->get('km.synchronizer_handler');
    }
    
    public function testStart()
    {
        $this->assertTrue($this->synchronizerHandler->start());
    }
}