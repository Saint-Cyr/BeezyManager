<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{   
    public function testAsSuperAdmin()
    {
        //Open a webbrowser
        $client1 = static::createClient();
        //Make sure the login page is display correctly
        $crawler = $client1->request('GET', '/login');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $this->assertContains('Sign In', $client1->getResponse()->getContent());
        $this->assertContains('Login', $client1->getResponse()->getContent());
        //Login as super-admin
        $crawler = $this->login($crawler, $client1, 'super-admin', 'test');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        //Make sure the front page is displayed correctly
        $this->assertContains('P', $client1->getResponse()->getContent());
        $this->assertContains('D1', $client1->getResponse()->getContent());
        $this->assertContains('POS #3', $client1->getResponse()->getContent());
        $this->assertContains('Dashboard #1', $client1->getResponse()->getContent());
        $this->assertContains('Dashboard #2', $client1->getResponse()->getContent());
        $this->assertContains('Dashboard #3', $client1->getResponse()->getContent());
        $this->assertContains('logo.jpg', $client1->getResponse()->getContent());
        //Go to the POS #3 and make sure it is displayed well
        $crawler = $client1->request('GET', '/admin/transaction/stransaction/create');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        //Go to the Dashboard #1 and make sure it is displayed well
        $crawler = $client1->request('GET', '/admin/dashboard');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        //Go to the objects list and make sure it is displayed as expected
        $crawler = $client1->request('GET', '/admin/transaction/stock/list');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $crawler = $client1->request('GET', '/admin/transaction/product/list');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $crawler = $client1->request('GET', '/admin/transaction/stransaction/list');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $crawler = $client1->request('GET', '/admin/transaction/sale/list');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $crawler = $client1->request('GET', '/admin/transaction/category/list');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $crawler = $client1->request('GET', '/admin/km/expenditure/list');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $crawler = $client1->request('GET', '/admin/km/branch/list');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $crawler = $client1->request('GET', '/admin/user/user/list');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        //Go to the objects "add new" and make sure it is displayed as expected
        $crawler = $client1->request('GET', '/admin/transaction/stock/create');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $crawler = $client1->request('GET', '/admin/transaction/product/create');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $crawler = $client1->request('GET', '/admin/transaction/stransaction/create');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $crawler = $client1->request('GET', '/admin/transaction/category/create');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $crawler = $client1->request('GET', '/admin/km/expenditure/create');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $crawler = $client1->request('GET', '/admin/km/branch/create');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $crawler = $client1->request('GET', '/admin/user/user/create');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
    }
    
    public function testLoginWithFalseCredentials()
    {
        //Open a webbrowser
        $client1 = static::createClient();
        //Make sure the login page is display correctly
        $crawler = $client1->request('GET', '/login');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $this->assertContains('Sign In', $client1->getResponse()->getContent());
        $this->assertContains('Login', $client1->getResponse()->getContent());
        //Login as super-admin
        $crawler = $this->login($crawler, $client1, 'False_username', 'False_Password');
        $this->assertEquals(200, $client1->getResponse()->getStatusCode());
        $this->assertContains('Invalid credentials.', $client1->getResponse()->getContent());
        
    }
    
    /*
     * this methode have 4 parameters:
     * @crawler : the robot
     * @client: the browser
     * @userName: the username 
     * @password: the password
     */
    public function login($crawler, $client1, $userName = '', $password = '')
    {
        //Fill the login form with the right credentials from the fixtures
        $form = $crawler->selectButton('btn_create_and_create')->form(array(
                                                            '_username'  => $userName,
                                                            '_password'  => $password,
                                                        ));
        //Submit the form in order to login
        $client1->submit($form);
        //The system redirect to the front page (/)
        $crawler = $client1->followRedirect();
    }
}
