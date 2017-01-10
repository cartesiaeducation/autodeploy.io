<?php

namespace AppBundle\Consumer;

use AppBundle\Manager\LogManager;
use AppBundle\Services\Authentification\AuthentificationChecker;
use Doctrine\Common\Persistence\ManagerRegistry;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AuthentificationCheckConsumer.
 */
class AuthentificationCheckConsumer extends AbstractConsumer
{
    /**
     * @var AuthentificationChecker
     */
    protected $authentificationChecker;

    /**
     * @param ManagerRegistry         $managerRegistry
     * @param LogManager              $logManager
     * @param AuthentificationChecker $authentificationChecker
     */
    public function __construct(ManagerRegistry $managerRegistry, LogManager $logManager, AuthentificationChecker $authentificationChecker)
    {
        parent::__construct($managerRegistry, $logManager);
        $this->authentificationChecker = $authentificationChecker;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $options = [
            'authentificationId' => null,
        ];
        $resolver->setDefaults($options);
        $resolver->setRequired(array_keys($options));
    }

    /**
     * {@inheritdoc}
     */
    protected function process(AMQPMessage $msg)
    {
        return $this->authentificationChecker->check($this->options['authentificationId']);
    }
}
