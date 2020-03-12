<?php

/*
 * This file is part of Components of BeezyManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) StCyrLabs <mapoukacyr@yahoo.fr>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\TransactionBundle\Service;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SaleHandlerTest extends WebTestCase
{
    private $em;
    private $application;
    private $saleHandler;
    private $sonataManagerOrm;


    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        
        $this->application = new Application(static::$kernel);
        $this->em = $this->application->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
        $this->saleHandler = $this->application->getKernel()->getContainer()->get('transaction.sale_handler');
        $this->sonataManagerOrm = $this->application->getKernel()->getContainer()->get('sonata.admin.manager.orm');
    }
    
    /*
     * By @Saint-Cyr MAPOUKA <mapoukacyr@yahoo.fr>
     * This test make sure that when the client send data to the server
     * during a STransaction Synchronization, and when the Data Structure is right
     * then it have to be persisted in the DB. Workflow:
     * 1- Build an InputData as it has been json_decode in the ApiController (Data Structure) with uniq st_synchrone_id
     * 2- Use the service saleHandler to process it (saleHandler:processSaleTransaction2(...))
     * 3- Fecht it from the DB based on the st_synchrone_id.
     * 4- Check the integrety of it structure.
     * 5- Remove it from the DB. (By canceling it) 
     */
    public function testProcessSaleTransaction2()
    {
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        $user = $this->em->getRepository('UserBundle:User')->find(1);
        //Step 1
        $order = [array('id' => 1, 'orderedItemCnt' => 2, 'totalPrice' => 234)];
        //Fake idSynchrone
        $idSynchrone = rand(0, 9999);
        $inputData = array('date_time' => '01-10-2015', 'total' => 234,
                           'st_synchrone_id' => $idSynchrone, 'order' => $order);
        
        //Step 2
        $this->saleHandler->processSaleTransaction2($inputData, $branch, $user);
        //Step 3
        $st = $this->em->getRepository('TransactionBundle:STransaction')->findOneBy(array('idSynchrone' => $idSynchrone));
        //Step 4
        $this->assertEquals($st->getIdSynchrone(), $idSynchrone);
        //Step 5
        //$this->em->remove($st);
        //$this->em->flush();
    }
    
    public function generateIdSynchrone()
    {
        
    }
}

