<?php

namespace TransactionBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class SaleAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('amount')
            //->add('stransaction')
            ->add('profit')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            //->add('stransaction')
            ->add('product')
            ->add('amount')
            ->add('profit')
            ->add('quantity')
            ->add('createdAt')
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
            ->add('product')
            ->add('quantity', 'integer', array('required' => false))
            ->add('amount', null, array('required' => false, 'label' => 'Amount (valid for untracked stock or discount application)'))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('amount')
        ;
    }   
    
    public function getExportFields() {
        return array('S. #ID'=>'id', 'T. #ID'=>'stransaction.id', 'T. Date'=>'stransaction.createdAt', 'Product'=>'product.name',
                     'T. Seller'=>'stransaction.user.name', 'T. Amount'=>'stransaction.totalAmount',
                     'T. Profit'=>'stransaction.profit' );
    }
    
    public function getDataSourceIterator() {
        $iterator = parent::getDataSourceIterator();
        $iterator->setDateTimeFormat('m/d/Y');
        return $iterator;
    }
    
    public function getExportFormats() {
        parent::getExportFormats();
        return ['xls', 'csv'];
    }
}
