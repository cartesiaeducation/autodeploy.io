<?php

namespace AppBundle\Consumer;

use AppBundle\Manager\LogManager;
use AppBundle\Manager\QueueManager;
use AppBundle\Manager\TaskManager;
use AppBundle\Services\Queue\QueueRunner;
use Doctrine\Common\Persistence\ManagerRegistry;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class QueueRunConsumer.
 */
class QueueRunConsumer extends AbstractConsumer
{
    /**
     * @var TaskManager
     */
    protected $queueManager;

    /**
     * @var QueueRunner
     */
    protected $queueRunner;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param LogManager      $logManager
     * @param QueueManager    $queueManager
     * @param QueueRunner     $queueRunner
     */
    public function __construct(ManagerRegistry $managerRegistry, LogManager $logManager, QueueManager $queueManager, QueueRunner $queueRunner)
    {
        parent::__construct($managerRegistry, $logManager);
        $this->queueManager = $queueManager;
        $this->queueRunner  = $queueRunner;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $options = [
            'queueId' => null,
        ];
        $resolver->setDefaults($options);
        $resolver->setRequired(array_keys($options));
    }

    /**
     * {@inheritdoc}
     */
    protected function process(AMQPMessage $msg)
    {
        return $this->queueRunner->run($this->options['queueId']);
    }
}
