<?php
/*
 * This file is part of Components of BeezyManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) iSTech <med@itechcar.com>
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
     * By @Saint-Cyr MAPOUKA <med@itechcar.com>
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
        $order = [array('id' => 1, 'orderedItemCnt' => 2, 'totalPrice' => 234, 'saleProfit' => 50)];
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
    
    public function testIsSellerGenius()
    {
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        //Step 1
        $order = [array('id' => 1, 'orderedItemCnt' => 2, 'totalPrice' => 234)];
        //Fake idSynchrone
        $idSynchrone = rand(0, 9999);
        $inputData = array('date_time' => '01-10-2015', 'total' => 234,
                           'st_synchrone_id' => $idSynchrone, 'order' => $order);
        
        //Case where seller is genius
        $outPut = $this->saleHandler->isSellerGenius('mapoukacyr@yahoo.fr');
        $this->assertEquals(array('response' => true, 'description' => ''), $outPut);
        //Case where seller is not genius
        $outPut2 = $this->saleHandler->isSellerGenius('unexisting@domaine.com');
        $this->assertEquals(array('response' => false, 'description' => 'the user related to the email: unexisting@domaine.com does not exist'), $outPut2);
    }
    
    public function testIsDataStructureValid()
    {
        $branch = $this->em->getRepository('KmBundle:Branch')->find(1);
        //Fake idSynchrone
        $idSynchrone = rand(0, 9999);
        //Case where data structure is right
        $order1 = [array('id' => 1, 'orderedItemCnt' => 2, 'totalPrice' => 234, 'saleProfit'=> 40)];
        $inputData = array('date_time' => '01-10-2015', 'total' => 234,
                           'st_synchrone_id' => $idSynchrone, 'order' => $order1,
                           'user_email' => 'mapoukacyr@yahoo.fr', 'branch_online_id' => 1);
        $outPut = $this->saleHandler->isDataStructureValid($inputData);
        $this->assertEquals($outPut, array('response' => true, 'description'=> ''));
        //Case where data structure is not valide
        $order1 = [array('id' => 1, 'orderedItemCnt' => 2, 'totalPrice' => 234)];
        $inputData = array('date_tim' => '01-10-2015', 'total' => 234,
                           'st_synchrone_id' => $idSynchrone, 'order' => $order1,
                           'user_email' => 'mapoukacyr@yahoo.fr', 'branch_online_id' => 1);
        $outPut = $this->saleHandler->isDataStructureValid($inputData);
        $this->assertEquals($outPut, array('response' => FALSE, 'description'=> 'date_time key does not exist in the Data Structure'));
        //Case where user is genius
        $order1 = [array(1, 'orderedItemCnt' => 2, 'totalPrice' => 234)];
        $inputData = array('date_time' => '01-10-2015', 'total' => 234,
                           'st_synchrone_id' => $idSynchrone, 'order' => $order1,
                           'email' => 'mapoukacyr@yahoo.fr');
    }
    
    public function testIsBranchValid()
    {
        //Assign a true value
        $branchOnlineId1 = 1;
        //Assign a fake branch value
        $branchOnlineId2 = 4000234;
        $outPut1 = $this->saleHandler->isBranchValid($branchOnlineId1);
        $outPut2 = $this->saleHandler->isBranchValid($branchOnlineId2);
        //Case where branch is valid
        $this->assertEquals(array('response' => true, 'description' => ''), $outPut1);
        //Case where branch is not valid
        $this->assertEquals(array('response' => false, 'description' => 'branch related to the #ID 4000234 not found from the server DB'), $outPut2);
    }
    
    public function generateIdSynchrone()
    {
        
    }
}

