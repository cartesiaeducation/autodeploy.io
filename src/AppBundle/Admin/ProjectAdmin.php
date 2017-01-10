<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class ProjectAdmin.
 */
class ProjectAdmin extends Admin
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
    protected $baseRouteName = 'admin_project';

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
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
            ->add('taskRetrieveStatus')
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
            ->add('name')
            ->add('user')
            ->add('branch')
            ->add('repository')
            ->add('taskRetrieveStatus')
            ->add('isEnabled')
            ->add('createdAt');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('description')
            ->add('user')
            ->add('taskRetrieveStatus')
            ->add('repository')
            ->add('createdAt')
            ->add('isEnabled', null, ['editable' => true]);
    }
}
