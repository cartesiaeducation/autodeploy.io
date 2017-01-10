<?php

namespace AppBundle\Form\Type\Capistrano;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SetupType.
 */
class SetupType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('keepReleases')
            ->add('directory', null, [
                'label' => 'Capistrano Root Directory',
            ])
            ->add('plugins', 'choice', [
                'label'   => 'Capistrano Plugins',
                'choices' => [
                    'capistrano/composer' => 'capistrano/composer',
                    'capistrano/symfony'  => 'capistrano/symfony',
                    'capistrano/npm'      => 'capistrano/npm',
                    'capistrano/bower'    => 'capistrano/bower',
                ],
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Form\Model\Capistrano\Setup',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_capistrano_conf';
    }
}
