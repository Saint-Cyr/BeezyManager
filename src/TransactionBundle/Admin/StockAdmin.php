<?php

namespace TransactionBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class StockAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('product.barcode')
            ->add('product')
            ->add('value')
            ->add('tracked')
            ->add('alertLevel')
            ->add('branch')
            ->add('name')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('tracked', null, array('editable' => true))
            ->add('branch')
            ->add('value', null, array('editable' => true))
            ->add('alertLevel', null, array('editable' => true))
            ->add('product')
            ->add('stocked')
            ->add('destocked')
            ->add('updateOther')
            ->add('alertStockCreatedAt')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
         ->with('Detail', array('class' => 'col-md-6'))
            ->add('name', null, array('label' => 'Tag Name'))
            ->add('value', null, array('label' => 'Value ( Quantity )', 'required' => false))
         ->end()
        
         ->with('Extra', array('class' => 'col-md-4'))
            ->add('product')
            ->add('alertLevel')
            ->add('branch')
         ->end()
         ->with('Extra', array('class' => 'col-md-4'))
            ->add('tracked')
            ->add('alertLevel') 
         ->end()
        ;
    }
    
    public function getBatchActions()
    {
        // retrieve the default batch actions (currently only delete)
        $actions = parent::getBatchActions();

        if (
          $this->hasRoute('edit') && $this->isGranted('EDIT') &&
          $this->hasRoute('delete') && $this->isGranted('DELETE')
            ) {
            $actions['resetValue'] = array(
                'label' => 'Init. Value ',
                'translation_domain' => 'SonataAdminBundle',
                'ask_confirmation' => true
            );
            
            $actions['resetAlert'] = array(
                'label' => 'Init. Alert',
                'translation_domain' => 'SonataAdminBundle',
                'ask_confirmation' => true
            );
            
            $actions['UntrackTrack'] = array(
                'label' => 'Untrack/Track',
                'translation_domain' => 'SonataAdminBundle',
                'ask_confirmation' => true
            );

        }

        return $actions;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
        ->with('Detail', array('class' => 'col-md-6'))
            ->add('value', null, array('label' => 'Qantity:', 'required' => false))
            ->add('name', null, array('label' => 'Tag Name'))
         ->end()
        
         ->with('Extra', array('class' => 'col-md-4'))
            ->add('product')
            ->add('alertLevel')
            ->add('branch')
         ->end()
        ;
    }
    
    public function preValidate($object) 
    {
        parent::preValidate($object);
        //Set the default value of the quantity to 0 if not input by the user
        if(!$object->getValue()){
            $object->setValue(0);
        }
    }
    
    public function preUpdate($newObject) {
        parent::preUpdate($newObject);
        //$em = $this->getModelManager()->getEntityManager($this->getClass());
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        
        $originalObject = $em->getUnitOfWork()->getOriginalEntityData($newObject);
        //var_dump($originalObject['value']);exit;
        //Case stock is added
        if($newObject->getValue() > $originalObject['value']){
            $newObject->setStocked(true);
            $newObject->setStockedAt(new \DateTime("now"));
            $newObject->setStockedValue($newObject->getValue() - $originalObject['value']);
            
            if($user->getName()){
                $newObject->setUpdateOther($user->getName());
            }else{
                $newObject->setUpdateOther($user->getUsername());
            }
            
        }
        //Case of destokation
        if($newObject->getValue() < $originalObject['value']){
            $newObject->setDestocked(true);
            $newObject->setDestockedAt(new \DateTime("now"));
            $newObject->setDestockedValue($originalObject['value'] - $newObject->getValue());
            
            if($user->getName()){
                $newObject->setUpdateOther($user->getName());
            }else{
                $newObject->setUpdateOther($user->getUsername());
            }
        }
        
    }
}
