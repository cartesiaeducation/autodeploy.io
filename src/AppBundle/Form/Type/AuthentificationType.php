<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AuthentificationType.
 */
class AuthentificationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name');

        if ($options['show_type']) {
            $builder
                ->add('type', 'choice', [
                    'choices'     => $options['data']->getTypeChoices(),
                    'empty_value' => '',
                ]);
        } else {
            $builder
                ->add('type', 'hidden');
        }

        $builder
            ->add('ssh', 'textarea');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Authentification',
            'show_type'  => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_bundle_authentification_type';
    }
}
