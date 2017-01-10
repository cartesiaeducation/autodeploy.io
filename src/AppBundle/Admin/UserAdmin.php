<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class UserAdmin.
 */
class UserAdmin extends Admin
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
    protected $baseRouteName = 'admin_user';

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
        $collection->remove('create');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('username')
            ->add('email')
            ->add('locale')
            ->add('roles', 'choice', [
                'choices' => [
                    'ROLE_USER'        => 'User',
                    'ROLE_SUPER_ADMIN' => 'Super Admin',
                ],
                'expanded' => false,
                'multiple' => true,
                'required' => true,
            ])
            ->add('enabled', null, [
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
            ->add('username')
            ->add('email')
            ->add('locale')
            ->add('lastLogin')
            ->add('createdAt')
            ->add('enabled');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('username')
            ->add('email')
            ->add('locale')
            ->add('roles', 'choice', [
                'choices' => [
                    'ROLE_USER'         => 'User',
                    'ROLE_ADMIN'        => 'Admin',
                    'ROLE_SUPER_ADMIN'  => 'Super Admin',
                    'ROLE_SONATA_ADMIN' => 'Sonata Admin',
                ],
                'multiple' => true,
            ])
            ->add('createdAt')
            ->add('enabled', null, ['editable' => true]);
    }
}
