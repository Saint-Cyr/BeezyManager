<?php

/*
 * This file is part of Components of KingManager project
 * By contributor S@int-Cyr MAPOUKA
 * (c) StCyrLabs <mapoukacyr@yahoo.fr>
 * For the full copyrght and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace UserBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRepositoryTest extends WebTestCase
{
    private $em;
    private $application;


    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        
        $this->application = new Application(static::$kernel);
        $this->em = $this->application->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
    }
    
    public function testGetByBranch()
    {
        //Test the case of VALLEY
        $users = $this->em->getRepository('UserBundle:User')->findBy(array('branch' => 2));
        $this->assertCount(1, $users);
        $this->assertEquals($users[0]->getBranch()->getName(), 'VALLEY');
    }
}
