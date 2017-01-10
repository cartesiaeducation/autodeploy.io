<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class LogAdmin.
 */
class LogAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by'    => 'createdAt',
    ];

    /**
     * {@inheritdoc}
     */
    protected $baseRouteName = 'admin_log';

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
        $collection->remove('create');
        $collection->remove('edit');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('description')
            ->add('user')
            ->add('branch')
            ->add('repository')
            ->add('isEnabled', null, [
                'required' => false,
                'label'    => 'Actif ?',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('consumerName')
            ->add('isSuccess')
            ->add('createdAt');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('consumerName')
            ->add('consumerOptions')
            ->add('isSuccess')
            ->add('createdAt')
            ->add('_action', 'actions', [
                    'actions' => [
                        'show' => [],
                    ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $filter)
    {
        $filter
            ->add('id')
            ->add('consumerName')
            ->add('consumerParams')
            ->add('consumerOptions')
            ->add('isSuccess')
            ->add('createdAt')
            ->add('message');
    }
}
