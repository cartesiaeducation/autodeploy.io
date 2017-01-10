<?php

namespace AppBundle\Form\Type\Capistrano;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FilesType.
 */
class FilesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('linkedFiles', 'bootstrap_collection', [
                'allow_add'       => true,
                'allow_delete'    => true,
                'type'            => new FileType(),
                'add_button_text' => 'Add new file',
            ])
            ->add('linkedDirs', 'bootstrap_collection', [
                'allow_add'       => true,
                'allow_delete'    => true,
                'type'            => new FileType(),
                'add_button_text' => 'Add new directory',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'AppBundle\Form\Model\Capistrano\Files',
            'cascade_validation' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_capistrano_files';
    }
}
