<?php
namespace TransactionBundle\Controller;
ini_set('memory_limit', '128M');
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TransactionBundle\Entity\STransaction;

class ApiController extends Controller
{
    
     /*
     * This action aims to receive and send data from and to the BSol
     * Client in order to upload sales transaction to the server 
     */
    public function postUpload2Action(Request $request)
    {
        
        //Get the input data sent by the front application (BSol Client, Android App, ...)
        $inputData = json_decode($request->getContent(), true);
        //Get the saleHandler service
        $saleHandler = $this->get('transaction.sale_handler');
        //validate the data structure
        $outPut1 = $saleHandler->isDataStructureValid($inputData);
        if(!$outPut1['response'])
        {
            return array('faild' => true, 'st_synchrone_id'=> $inputData['st_synchrone_id'], 'faildMessage' => $outPut1['description']);
        }
        // Validate the authenticity of the BSol Client Application. In order to achieve this,
        //check the authenticity of the branch related to BSol Client
        $outPut2 = $saleHandler->isBranchValid($inputData['branch_online_id']);
        if(!$outPut2['response'])
        {
            return array('faild' => true, 'st_synchrone_id'=> $inputData['st_synchrone_id'], 'faildMessage' => $outPut2['description']);
        }
        //Validate the authenticity of the BSol Client seller (the one that have made the Transaction)
        $outPut3 = $saleHandler->isSellerGenius($inputData['user_email']);
        if(!$outPut3['response'])
        {
            return array('faild' => true, 'st_synchrone_id'=> $inputData['st_synchrone_id'], 'faildMessage' => $outPut3['description']);
        }
        
        return array('faild' => true, 'st_synchrone_id'=> $inputData['st_synchrone_id'], 'faildMessage' => 'test....');
    }
    
     /*
     * @Deprecated since 0.4-server
     * This action aims to receive and send data from and to the BSol
     * Client in order to upload sales transaction to the server online
     */
    public function postUploadAction(Request $request)
    {        
        //Verbose
        $faild = false;
        $faildMessage = 'successful';

        $em = $this->getDoctrine()->getManager();
        //Get the input data sent by the front application
        $inputData = json_decode($request->getContent(), true);
        //Validate the data structure and it content
        if(array_key_exists('st_synchrone_id', $inputData)&&
           array_key_exists('user_email', $inputData)&&
           array_key_exists('order', $inputData)&&
           array_key_exists('total', $inputData)&&
           array_key_exists('date_time', $inputData)&&
           array_key_exists('branch_online_id', $inputData))
        {
            // As data structure is valid, then jump to the next step
            // Process evvery thing here...
        }else{
            return array('faild' => "Invalid data structure");
        }
        
        //If stransaction already exist, then get out.
        $stransaction = $em->getRepository('TransactionBundle:STransaction')
            ->findOneBy(array('idSynchrone' => $inputData['st_synchrone_id']));
        
        if($stransaction){
            $faild = true;
            $faildMessage = 'transaction already exists';
            return array('faild' => $faild,
                     'remove_st' => true,
                     'faild_message' => $faildMessage,
                     'st_synchrone_id' => $stransaction->getIdSynchrone());
        }

        //Get the branch from the user object
        $user = $em->getRepository('UserBundle:User')
                   ->findOneBy(array('email' => $inputData['user_email']));
        
        $branch = $user->getBranch();
        
        //Make sure object exist
        if((!$user) || (!$branch)){
            $faild = true;
            $faildMessage = 'user or branch does not exist';
            return array('faild' => $faild,
                     'faild_message' => $faildMessage,
                     'st_synchrone_id' => 'null');
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
                     'faild_message' => $faildMessage,
                     'st_synchrone_id' => $stransaction->getIdSynchrone());
    }
    
    /*
     * @deprecated since 0.3-server
     */
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
        
        //Because Doctrine load all the related field, we have to build new Data structure
        $products[] = null;
        foreach ($stocks as $s){
            $p = $s->getProduct();
            $products[] = array('name' => $p->getName(), 'barcode' => $p->getBarcode(),
                                'unit_price' => $p->getUnitPrice(), 'id' => $p->getId());
        }
        
        $Branch = array('name' => $branch->getName(), 'id' => $branch->getId());
        //return $Branch;
        
        //Get all users
        $users = $em->getRepository('UserBundle:User')->findBy(array('branch' => $data['branch_id']));
        $Users[] = null;
        foreach ($users as $u){
            $Users[] = array('username' => $u->getUsername(), 'email' => $u->getEmail(), 'roles' => $u->getRoles());
        }
        
        //return array('products' => $products, 'branch' => $Branch, 'users' => $Users);
        return array('products' => $products, 'users' => $Users, 'status' => true,
                     'message' => 'successfull download', 'branch' => $Branch);
    }
    
    /*
     * @deprecated since 0.4-server
     * need to be remove after implementing all functional test
     * cases
     */
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
        
        return 'successfull transaction!';
    }
    
    /*This method fetch data to display on dashboard for third party
     * technology
     */
    public function getDashboardAction(Request $request)
    {
        //Get the statistic handler service
        $statisticHandler = $this->get('km.statistic_handler');
        //Get the entity manager
        $em = $this->getDoctrine()->getManager();
        //Get all the branches
        $branches = $em->getRepository('KmBundle:Branch')->findAll();
        //Prepare resum for all branch
        $totalSale = null;
        $totalProfit = null;
        $totalExpenditure = null;
        $totalBalance = null;

        foreach ($branches as $b){
            //Hydrate every branch
            $statisticHandler->setBranchFlyData($b);
            $totalSale = $totalSale + $b->getFlySaleAmount();
            $totalExpenditure = $totalExpenditure + $b->getFlyExpenditureAmount();
            $totalProfit = $totalProfit + $b->getFlyProfitAmount();
            $totalBalance = $totalBalance + $b->getFlyBalanceAmount();
        }
        
        $outPut['general_data'] =  array('totalSale' => $totalSale,
                                        'totalProfit' => $totalProfit,
                                        'totalBalance' => $totalBalance,
                                        'totalExpenditure' => $totalExpenditure,);
        $outPut['barchart'] = array('jan' => 10000, 'feb' => 1500, 'mar' => 2000,
                                    'apr' => 2500, 'may' => 3000, 'jun' => 3500,
                                    'jul' => 4000, 'aug' => 4500, 'sep' => 5000,
                                    'oct' => 5500, 'nov' => 6000, 'dec' => 6500);
        $outPut['pie_chart'] = array('Product Name 1' => 2400000, 'Product Name 2' => 1700000,
                                     'Product Name 3' => 1300000, 'Product Name 4' => 750000);
        return $outPut;
    }
    
    /*This method serves the third party technology (Android App, ...)
     * 
     */
    public function postExternLoginAction(Request $request)
    {
        //Get the data sent by the third party technology
        $inputData = json_decode($request->getContent(), true);
        //Get the ApiHandler service
        $apiHandler = $this->get('km.api_handler');
        //Login
        $outPut = $apiHandler->login($inputData);
        return $outPut;
    }
    
    /*This method provide data (sale, profit, expenditure) for 
     * each branch
     */
    public function getBranchesDataAction(Request $request)
    {
        //Get the statistic handler service
        $statisticHandler = $this->get('km.statistic_handler');
        //Get the entity manager
        $em = $this->getDoctrine()->getManager();
        //Get all the branches
        $branches = $em->getRepository('KmBundle:Branch')->findAll();
        
        foreach ($branches as $b){
            //Hydrate every branch
            $statisticHandler->setBranchFlyData($b);
        }
        
        //put the branche in the right data structure
        foreach ($branches as $b){
            
            $branchesTab[] = array('name' => $b->getName(), 'branch_sale_amount' => $b->getFlySaleAmount(),
                                   'branch_profit_amount' => $b->getFlyProfitAmount(),
                                   'branch_expenditure_amount' => $b->getFlyExpenditureAmount(),
                                   'branch_id' => $b->getId());
        }
        
        return $branchesTab;
    }
    
    /*This method provide report data for a particular branch
     * @param init_date: "mm-dd-yyyy"
     * @param fin_date: "mm-dd-yyyy"
     * @param branch_id: Integer
     */
    public function postBranchReportAction(Request $request)
    {
        //Get the branch name based on the branch #ID sent from the client
        $branch = $this->getDoctrine()->getManager()->getRepository('KmBundle:Branch')
                                          ->find($request->get('branch_id'));
        //Make sure branch exist in DB
        if(!$branch){
            return array('branch not found');
        }
        $outPut['branch_name'] = $branch->getName();
        $outPut['ini_date'] = $request->get('ini_date');
        $outPut['fin_date'] = $request->get('fin_date');
        $outPut['general_data'] =  array('totalSale' => 100000,
                                        'totalProfit' => 25000,
                                        'totalBalance' => 90000,
                                        'totalExpenditure' => 10000);
        $outPut['barchart'] = array('jan' => 10000, 'feb' => 1500, 'mar' => 2000,
                                    'apr' => 2500, 'may' => 3000, 'jun' => 3500,
                                    'jul' => 4000, 'aug' => 4500, 'sep' => 5000,
                                    'oct' => 5500, 'nov' => 6000, 'dec' => 6500);
        $outPut['pie_chart'] = array('Product Name 1' => 2400000, 'Product Name 2' => 1700000,
                                     'Product Name 3' => 1300000, 'Product Name 4' => 750000);
        
        return $outPut;
    }
    
    /*This method provide report data for a particular branch
     * 
     */
    public function getFaqAction()
    {
        //Get the FAQ from DB...
        $faq_1 = array('url' => 'https://www.google.com/images/1.jpg',
                       'content' => 'Lorem ipsum dolor sit amet, mei volutpat scribentur ne. Facete omittam in cum,'
                                    . 'mel nibh vide no. Eam ignota referrentur ei. Error iudico vel in, ea vix aliquam '
                                    . 'feugiat, eam iisque delenit in. Ornatus suavitate assentior an mei, his lorem '
                                    . 'labore cu, sonet possim in has. Vel ea hendrerit evertitur, option adipiscing '
                                    . 'in nam, singulis efficiendi ex duo.');
         $faq_2 = array('url' => 'https://www.google.com/images/2.jpg',
                       'content' => 'Lorem ipsum dolor sit amet, mei volutpat scribentur ne. Facete omittam in cum,'
                                    . 'mel nibh vide no. Eam ignota referrentur ei. Error iudico vel in, ea vix aliquam '
                                    . 'feugiat, eam iisque delenit in. Ornatus suavitate assentior an mei, his lorem '
                                    . 'labore cu, sonet possim in has. Vel ea hendrerit evertitur, option adipiscing '
                                    . 'in nam, singulis efficiendi ex duo.');
          $faq_3 = array('url' => 'https://www.google.com/images/3.jpg',
                       'content' => 'Lorem ipsum dolor sit amet, mei volutpat scribentur ne. Facete omittam in cum,'
                                    . 'mel nibh vide no. Eam ignota referrentur ei. Error iudico vel in, ea vix aliquam '
                                    . 'feugiat, eam iisque delenit in. Ornatus suavitate assentior an mei, his lorem '
                                    . 'labore cu, sonet possim in has. Vel ea hendrerit evertitur, option adipiscing '
                                    . 'in nam, singulis efficiendi ex duo.');
           $faq_4 = array('url' => 'https://www.google.com/images/4.jpg',
                       'content' => 'Lorem ipsum dolor sit amet, mei volutpat scribentur ne. Facete omittam in cum,'
                                    . 'mel nibh vide no. Eam ignota referrentur ei. Error iudico vel in, ea vix aliquam '
                                    . 'feugiat, eam iisque delenit in. Ornatus suavitate assentior an mei, his lorem '
                                    . 'labore cu, sonet possim in has. Vel ea hendrerit evertitur, option adipiscing '
                                    . 'in nam, singulis efficiendi ex duo.');
             
        return array($faq_1, $faq_2, $faq_3, $faq_4);
    }
    
    /*
     * This method allow client to save the app setting
     */
    public function postSettingAction(Request $request)
    {
        //Get the setting parameters
        $language = $request->get('language');
        $customerId = $request->get('customer_id');
        //Save them in DB.
    }
}