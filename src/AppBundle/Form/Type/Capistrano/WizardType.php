<?php

namespace AppBundle\Form\Type\Capistrano;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WizardType.
 */
class WizardType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Project Name',
            ])
            ->add('repositoryUrl')
            ->add('scm', 'choice', [
                'choices' => [
                    'git' => 'GIT',
                    'svn' => 'Subversion',
                ],
                'label' => 'Repositoty Type',
            ])
            ->add('repositoryTree', null, [
                'label' => 'Repository Path',
            ])
            ->add('setup', new SetupType())
            ->add('files', new FilesType())
            ->add('environments', 'bootstrap_collection', [
                'allow_add'       => true,
                'allow_delete'    => true,
                'add_button_text' => 'Add Environment',
                'type'            => new EnvironmentsType(),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Form\Model\Capistrano\Wizard',
            'cascade_validation' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_capistrano_wizard';
    }
}
