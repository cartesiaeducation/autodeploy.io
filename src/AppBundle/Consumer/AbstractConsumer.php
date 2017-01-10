<?php

namespace AppBundle\Consumer;

use AppBundle\Manager\LogManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract Class AbstractConsumer.
 */
abstract class AbstractConsumer implements ConsumerInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    /**
     * @var LogManager
     */
    protected $logManager;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param LogManager      $logManager
     */
    public function __construct(ManagerRegistry $managerRegistry, LogManager $logManager)
    {
        $this->em         = $managerRegistry->getManager();
        $this->logManager = $logManager;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(AMQPMessage $msg)
    {
        $log = $this->logManager->create(get_class($this), $msg->body);

        try {
            $this->setDefaultConfig(unserialize($msg->body));

            $log->setConsumerOptions($this->options);
            $log->setMessage($this->process($msg));
            $log->setIsSuccess(true);
        } catch (\Exception $e) {
            $log->setMessage($e->getMessage());
            $log->setIsSuccess(false);
        }

        $this->logManager->save($log);
        $this->em->clear();
    }

    /**
     * Set default config.
     *
     * @param array $options
     */
    protected function setDefaultConfig(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * @param AMQPMessage $msg
     *
     * @return mixed
     */
    abstract protected function process(AMQPMessage $msg);

    /**
     * @param OptionsResolver $resolver
     */
    abstract protected function configureOptions(OptionsResolver $resolver);
}
