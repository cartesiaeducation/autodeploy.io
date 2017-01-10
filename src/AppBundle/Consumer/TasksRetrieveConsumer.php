<?php

namespace AppBundle\Consumer;

use AppBundle\Manager\LogManager;
use AppBundle\Manager\TaskManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TasksRetrieveConsumer.
 */
class TasksRetrieveConsumer extends AbstractConsumer
{
    /**
     * @var TaskManager
     */
    protected $taskManager;

    /**
     * @param ManagerRegistry $managerRegistry
     * @param LogManager      $logManager
     * @param TaskManager     $taskManager
     */
    public function __construct(ManagerRegistry $managerRegistry, LogManager $logManager, TaskManager $taskManager)
    {
        parent::__construct($managerRegistry, $logManager);
        $this->taskManager = $taskManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $options = [
            'projectId' => null,
        ];
        $resolver->setDefaults($options);
        $resolver->setRequired(array_keys($options));
    }

    /**
     * {@inheritdoc}
     */
    protected function process(AMQPMessage $msg)
    {
        return $this->taskManager->retrieveList($this->options['projectId']);
    }
}
