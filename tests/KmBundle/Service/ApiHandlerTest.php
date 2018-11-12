<?php

/*
 * This file is part of Components of BeezyManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) iTech <mapoukacyr@yahoo.fr>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Tests\KmBundle\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class ApiHandlerTest extends WebTestCase
{
    private $em;
    private $application;
    private $apiHandler;


    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        
        $this->application = new Application(static::$kernel);
        $this->em = $this->application->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
        $this->apiHandler = $this->application->getKernel()->getContainer()->get('km.api_handler');
    }
    
    public function testLogin()
    {
        $apiHandler = $this->apiHandler;
        
       //Case where login successfully for Super - Administrator
       $inputData = array('username' => 'super-admin', 'password' => 'test');
       $outPut = $apiHandler->login($inputData);
       $this->assertEquals($outPut->getData(), array('name' => 'Saint-Cyr', 'email' => 'super-admin@domain.com'));
       
       //Case login faild: wrong username
       $inputData = array('username' => 'SomeFalseUsername', 'password' => 'test');
       $outPut = $apiHandler->login($inputData);
       $this->assertEquals($outPut->getData(), array('Bad credentials.'));
       
       //Case login faild: wrong password
       $inputData = array('username' => 'verifier1', 'password' => 'SomeFalsePassword');
       $outPut = $apiHandler->login($inputData);
       $this->assertEquals($outPut->getData(), array('Bad credentials.'));
    }
    
   
}

