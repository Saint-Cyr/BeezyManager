<?php

namespace TransactionBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

class StockAdminController extends CRUDController
{
    
    public function batchActionResetValue(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }
        
        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();
        
        // do the merge work here
        try {
            foreach ($selectedModels as $selectedModel) {
                //Reset the stock value to 0 
                $selectedModel->setValue(0);
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
    
    public function batchActionResetAlert(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        
        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();
        
        
        try {
            foreach ($selectedModels as $selectedModel) {
                //Reset the alert value of barcode
                $selectedModel->setAlertLevel(0);
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
    
    public function batchActionUntrackTrack(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        
        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();
        
        
        try {
            foreach ($selectedModels as $selectedModel) {
                //If allready tracked, then untrack or the reverse
                if($selectedModel->isTracked()){
                    $selectedModel->setTracked(false);
                    
                }elseif(!$selectedModel->isTracked()){
                    $selectedModel->setTracked(true);
                }
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
