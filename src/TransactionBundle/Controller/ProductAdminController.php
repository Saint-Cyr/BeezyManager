<?php

namespace TransactionBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductAdminController extends CRUDController
{
    
    /**
     * Create action.
     *
     * @return Response
     *
     * @throws AccessDeniedException If access is not granted
     */
    public function createAction()
    {
        //Read products list from a CSV file and save in DB if aplicable
                if(true){
                    /*$inputFileType = 'CSV';
                    $inputFileName = getcwd().'/1.csv';
                    $sheetname = 'Data Sheet #2';

                    
                    
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                    $spreadsheet = $reader->load($inputFileName);
                    
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();*/
                    
                }
        //Read products list with eventually other information and write them in a CSV file
                if(true){
                    //load spreadsheet
                    /*$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(getcwd().'/1.xlsx');

                    //change it
                    $sheet = $spreadsheet->getActiveSheet();
                    
                    //Write all the Sale Transaction important data for analyses
                    $stransactions = $this->getDoctrine()->getManager()
                                          ->getRepository('TransactionBundle:STransaction')
                                          ->findAll();
                    
                    foreach ($stransactions as $index => $st){
                        if($st->getUser()){
                            $userName = $st->getUser()->getName();
                        }else{
                            $userName = 'Unknown';
                        }
                        $sheet->setCellValue('A'.$index, $userName);
                        $sheet->setCellValue('B'.$index, $st->getTotalAmount());
                    }
                                        

                    //write it again to Filesystem with the same name (=replace)
                    $writer = new Xlsx($spreadsheet);
                    $writer->save(getcwd().'/1.xlsx');*/
                }
                
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $this->admin->checkAccess('create');

        $class = new \ReflectionClass($this->admin->hasActiveSubClass() ? $this->admin->getActiveSubClass() : $this->admin->getClass());

        if ($class->isAbstract()) {
            return $this->render(
                'SonataAdminBundle:CRUD:select_subclass.html.twig',
                array(
                    'base_template' => $this->getBaseTemplate(),
                    'admin' => $this->admin,
                    'action' => 'create',
                ),
                null,
                $request
            );
        }

        $object = $this->admin->getNewInstance();

        $preResponse = $this->preCreate($request, $object);
        if ($preResponse !== null) {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);
        $form->handleRequest($request);
        

        if ($form->isSubmitted()) {
            //By S@int-Cyr
            //Get the barcodeHandler service
            $barcodeHandler = $this->get('km.barcode_handler');
            //Generate the barcode
            $code = $barcodeHandler->generateBarcode();
            
            $object->setBarcode($code);
            //TODO: remove this check for 4.0
            if (method_exists($this->admin, 'preValidate')) {
                $this->admin->preValidate($object);
            }
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode($request) || $this->isPreviewApproved($request))) {
                $this->admin->checkAccess('create', $object);
                

                try {
                    $object = $this->admin->create($object);

                    if ($this->isXmlHttpRequest()) {
                        return $this->renderJson(array(
                            'result' => 'ok',
                            'objectId' => $this->admin->getNormalizedIdentifier($object),
                        ), 200, array());
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_create_success',
                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                            'SonataAdminBundle'
                        )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($object);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->addFlash(
                        'sonata_flash_error',
                        $this->trans(
                            'flash_create_error',
                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                            'SonataAdminBundle'
                        )
                    );
                }
            } elseif ($this->isPreviewRequested()) {
                // pick the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('Symfony\Bridge\Twig\Extension\FormExtension')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'create',
            'form' => $view,
            'object' => $object,
        ), null);
    }
    
    public function batchActionGenerate(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        
        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();
        
        // do the merge work here
        try {
            foreach ($selectedModels as $selectedModel) {
                //Get the barcodeHandler service
                $barcodeHandler = $this->get('km.barcode_handler');
                //Generate the barcode
                $code = $barcodeHandler->generateBarcode();

                $selectedModel->setBarcode($code);
            }
            
            $modelManager->update($selectedModel);
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'flash_batch_activation_error');

            return new RedirectResponse(
                $this->admin->generateUrl('list', $this->admin->getFilterParameters())
            );
        }

        $this->addFlash('sonata_flash_success', $this->get('translator')->trans(' successful operations !'));

        return new RedirectResponse(
            $this->admin->generateUrl('list', $this->admin->getFilterParameters())
        );
    }
    
    public function batchActionLockBarcode(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        
        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();
        
        
        try {
            foreach ($selectedModels as $selectedModel) {
                //Lock the product barcode
                $selectedModel->setLocked(true);
            }
            
            $modelManager->update($selectedModel);
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'flash_batch_activation_error');

            return new RedirectResponse(
                $this->admin->generateUrl('list', $this->admin->getFilterParameters())
            );
        }

        $this->addFlash('sonata_flash_success', $this->get('translator')->trans(' successful operations !'));

        return new RedirectResponse(
            $this->admin->generateUrl('list', $this->admin->getFilterParameters())
        );
    }
}
