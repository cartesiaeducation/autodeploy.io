<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\DataTransformer\UserToEmailTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CollaboratorType.
 */
class CollaboratorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $builder->create('user', 'email', [
                'label' => 'Email',
            ])
            ->addModelTransformer(new UserToEmailTransformer($options['entity_manager']))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'     => 'AppBundle\Entity\Collaborator',
            'entity_manager' => null,
        ]);

        $resolver->setRequired([
            'entity_manager',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_bundle_collaborator_type';
    }
}
