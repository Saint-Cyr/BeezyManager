<?php

namespace TransactionBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ReportAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

       $formMapper
           ->with('Data', array('class' => 'col-md-4'))
               ->add('e1', 'entity', array('class' => 'TransactionBundle:Category', 'label' => 'E1: One by column (Ex: Secre)', 'required' => true))
               ->add('e2', 'entity', array('class' => 'TransactionBundle:Category', 'label' => 'E2: All in one column (Ex: Shop)', 'required' => true))
               ->add('e3', 'entity', array('class' => 'KmBundle:Branch', 'label' => 'Branch', 'required' => true))
           ->end();

       $formMapper
        ->with('Custom period range', array('class' => 'col-md-4'))
           ->add('type', 'choice', array('expanded' => true,
                                         'label' => ' ',
                                         'required' => false,
                                         'choices' => array('Today' => 'today',
                                                            'Yesterday' => 'yesterday'),))
        ->end()
        ->with('Periods', array('class' => 'col-md-4'))
           ->add('initDate', 'sonata_type_date_picker', array('required' => false))
           ->add('finitDate', 'sonata_type_date_picker', array('required' => false))
        ->end()
        ;
    }
}
