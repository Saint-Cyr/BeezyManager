<?php

/*
 * This file is part of Components of BeezyManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) TinzapaTech <mapoukacyr@yahoo.fr>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\TransactionBundle\Service;

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
        //Get the barcodeHandler service
        $barcodeHandler = $this->application->getKernel()->getContainer()->get('km.barcode_handler');
        //Generate a code
        $code = $barcodeHandler->generateBarcode();
        //case 1: check the length of the code ( it have to be 13 )
        $this->assertEquals(13, strlen($code));
        //Case 2: check the unicity of the generated code
        $code1 = $barcodeHandler->generateBarcode();
        $code2 = $barcodeHandler->generateBarcode();
        $this->assertNotEquals($code1, $code2);
        
        $code1 = $barcodeHandler->generateBarcode();
        $code2 = $barcodeHandler->generateBarcode();
        $this->assertNotEquals($code1, $code2);
        
        $code1 = $barcodeHandler->generateBarcode();
        $code2 = $barcodeHandler->generateBarcode();
        $this->assertNotEquals($code1, $code2);
        
        $code1 = $barcodeHandler->generateBarcode();
        $code2 = $barcodeHandler->generateBarcode();
        $this->assertNotEquals($code1, $code2);
        
        
    }
}