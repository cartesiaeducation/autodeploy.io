<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class QueueType.
 */
class QueueType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('task', 'entity', [
                'class'         => 'AppBundle:Task',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('t')
                        ->andWhere('t.project = :projectId')
                        ->andWhere('t.isEnabled = true')
                        ->setParameter('projectId', $options['project']->getId())
                        ->orderBy('t.name', 'ASC');
                },
            ])
            ->add('environment', 'entity', [
                'empty_value'   => '',
                'class'         => 'AppBundle:Environment',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('e')
                              ->andWhere('e.project = :projectId')
                              ->setParameter('projectId', $options['project']->getId())
                              ->orderBy('e.name', 'ASC');
                },
            ])
            ->add('branch', null, [
                'label' => 'Capistrano Branch',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Queue',
            'project'    => null,
        ]);
        $resolver->setRequired([
            'project',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_bundle_queue_type';
    }
}
