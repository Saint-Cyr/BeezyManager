<?php

namespace KmBundle\Service;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use FOS\RestBundle\View\View;
class ApiHandler
{
    //To store the entity manager
    private $em;
    
    public function __construct($em, $factory, $user_provider, $securityToken, $container) 
    {
        $this->em = $em;
        $this->factory = $factory;
        $this->user_provider = $user_provider;
        $this->security = $securityToken;
        $this->container = $container;
    }
    
    /*
     * This method is used to login the super-admin from a third party technology
     * most of the case a mobile application. Notice that user can either his username
     * or his email to login
     */
    public function login($inputData)
    {
        //if user is authentic then get $user object otherwise, get false;
        $user = $this->isAuthentic($inputData['username'], $inputData['password']);
        
        if($user){
            
            $response = array('name' => $user->getName(), 'email' => $user->getEmail());
            
            return View::create($response, 200);
        }
        
        return View::create(array('Bad credentials.'), 401);
    }
    
    public function isAuthentic($username, $password)
    {
        /* Validate the User */
        $factory = $this->factory;
        
        //To avoid thrownig an exception by FOSUserBundle in the case where username or Email is wrong
        $userByUserName = $this->em->getRepository('UserBundle:User')->findOneBy(array('username' => $username));
        $userByEmail= $this->em->getRepository('UserBundle:User')->findOneBy(array('email' => $username));
        
        if(!$userByUserName && !$userByEmail){
            return false;
        }elseif($userByUserName){
            $user = $userByUserName;
            $userByEmail = false;
        }elseif($userByEmail){
            $user = $userByEmail;
            $username = $user->getUsername();
        }
        
        //The username is right
        $user = $this->user_provider->loadUserByUsername($username);
        
        $encoder = $factory->getEncoder($user);
        $validated = $encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt());
        if (!$validated) {
            http_response_code(400);
            return false;
        } else {
            
            $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
            //now the user is logged in
            $this->security->setToken($token);
            //now dispatch the login event
            //$request = $this->container->get("router.request_context");
            //$event = new InteractiveLoginEvent($request, $token);
            //$this->container->get("event_dispatcher")->dispatch("security.interactive_login", $event);
        return $user;
        
        }
    }
    

}
