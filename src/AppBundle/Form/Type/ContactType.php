<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ContactType.
 */
class ContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email')
            ->add('subject')
            ->add('message', 'textarea');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label_format'       => 'contact.form.%name%',
            'translation_domain' => 'form',
            'data_class'         => 'AppBundle\Form\Model\Contact',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_bundle_contact_type';
    }
}
