<?php

namespace TransactionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TransactionBundle\Entity\STransaction;

class ApiController extends Controller
{
    
    public function postDownloadAction(Request $request)
    {
        //we need to work with DB
        $em = $this->getDoctrine()->getManager();
        //Get the input Data
        $data = json_decode($request->getContent(), true);
        //Get all product for the branch $branch and the second arg[] mean that it
        
        //If $branch does not exist then inform the client immediatly
        if(!$data['branch_id']){
            return array('status' => false, 'message' => 'branch not found');
        }
        
        $branch = $em->getRepository('KmBundle:Branch')->find($data['branch_id']);
        if(!$branch){
            return array('status' => false, 'message' => 'branch #ID: '.$data['branch_id'].' not found.');
        }
        
        $stocks = $em->getRepository('TransactionBundle:Stock')->getTrackedByBranch($branch, true);
        
        foreach ($stocks as $s){
            $p = $s->getProduct();
            $products[] = array('name' => $p->getName(), 'barcode' => $p->getBarcode(),
                                'unit_price' => $p->getUnitPrice(), 'id' => $p->getId());
        }
        
        
        //Get all users
        $users = $em->getRepository('UserBundle:User')->findBy(array('branch' => $data['branch_id']));
        return array('products' => $products, 'users' => $users, 'status' => true,
                     'message' => 'successfull download', 'branch' => $branch);
    }
    
    public function postUploadAction(Request $request)
    {
        //Verbose
        $faild = false;
        $faildMessage = 'successful';

        $em = $this->getDoctrine()->getManager();
        //Get the input data sent by the front application
        $inputData = json_decode($request->getContent(), true);
        //Make sure all the products exits
        foreach ($inputData['order'] as $key => $s){
            //Fetch the product
            if(!$s['id']){
                $faild = true;
                $faildMessage = "the property onlineId of product  #".$key." in order[] not defined";
                return array('faild' => $faild, 'message' => $faildMessage);
            }
            
            $product = $em->getRepository('TransactionBundle:Product')->find($s['id']);
            if(!$product){
                $faild = true;
                $faildMessage = 'product for #ID: '.$s['id'].' not found in DB.';
                return array('faild' => $faild, 'message' => $faildMessage);
            }
        }
        
        //If stransaction already exist, then get out.
        $stransaction = $em->getRepository('TransactionBundle:STransaction')
            ->findOneBy(array('idSynchrone' => $inputData['st_synchrone_id']));
        
        if($stransaction){
            $faild = true;
            $faildMessage = 'transaction already exists';
            return array('faild' => $faild,
                     'faildMessage' => $faildMessage,
                     'st_synchrone_id' => $stransaction->getIdSynchrone());
        }

        //Get the branch from the user object
        $user = $em->getRepository('UserBundle:User')
                   ->findOneBy(array('email' => $inputData['user_email']));
        
        $branch = $em->getRepository('KmBundle:Branch')
                     ->find($inputData['branch_online_id']);
        
        //Make sure object exist
        if((!$user) || (!$branch)){
            $faild = true;
            $faildMessage = 'user or branch does not exist';
            return array('faildMessage' => $faildMessage, 'status' => $faild);
        }else{
            //Get the STransaction handler service
            $saleHandler = $this->get('transaction.sale_handler');
            //Process the sale transaction
            $saleHandler->processSaleTransaction2($inputData, $branch, $user);
        }
        //Fetch the idSynchrone to give it back in order for the client to remove it from it cache
        $stransaction = $em->getRepository('TransactionBundle:STransaction')
            ->findOneBy(array('idSynchrone' => $inputData['st_synchrone_id']));
        
        return array('faild' => $faild,
                     'faildMessage' => $faildMessage,
                     'st_synchrone_id' => $stransaction->getIdSynchrone());
    }

    public function postSaleTransactionAction(Request $request)
    {
        //Get the input data sent by the front application
        $inputData = json_decode($request->getContent(), true);
        //Get the branch from the user object
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        $branch = $user->getBranch();
        
        $data = $inputData['data'];
        
        //Get the STransaction handler service
        $saleHandler = $this->get('transaction.sale_handler');
        //Process the sale transaction
        
        $saleHandler->processSaleTransaction($data, $branch);
        
        $response = new Response($this->get('translator')->trans('Successfull transaction!'));
                            
        $response->headers->set('Access-Control-Allow-Origin', 'http://127.0.0.1');
        return $response;
        //return View::create(array('info' => 'user of verifier_token: '.$inputData['verifier_token'].' has been logged out successfully.'), 200);
        
        return 'successfull transaction!';
    }
}