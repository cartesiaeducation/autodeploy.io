<?php

namespace AppBundle\Form\Type\Capistrano;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EnvironmentsType.
 */
class EnvironmentsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('env', 'choice', [
                'empty_value' => '',
                'choices'     => [
                    'prod'    => 'prod',
                    'staging' => 'staging',
                    'dev'     => 'dev',
                ],
            ])
            ->add('branch')
            ->add('deployTo')
            ->add('tmp', null, [
                'label' => 'Temporary Directory',
            ])
            ->add('logLevel', 'choice', [
                'choices' => [
                    'debug' => 'debug',
                    'info'  => 'info',
                    'warn'  => 'warning',
                    'error' => 'error',
                    'fatal' => 'fatal',
                ],
            ])
            ->add('servers', 'bootstrap_collection', [
                'allow_add'       => true,
                'allow_delete'    => true,
                'add_button_text' => 'Add Server',
                'type'            => new ServerType(),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Form\Model\Capistrano\Environments',
            'cascade_validation' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_capistrano_env';
    }
}
