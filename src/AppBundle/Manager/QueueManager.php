<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Environment;
use AppBundle\Entity\Project;
use AppBundle\Entity\Queue;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * Class QueueManager.
 */
class QueueManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Producer
     */
    private $producer;

    /**
     * @param EntityManagerInterface $em
     * @param Producer               $producer
     */
    public function __construct(EntityManagerInterface $em, Producer $producer)
    {
        $this->em       = $em;
        $this->producer = $producer;
    }

    /**
     * @param Project $project
     * @param User    $user
     * @param Task    $task
     *
     * @return Queue
     */
    public function create(Project $project, User $user, Task $task)
    {
        $queue = new Queue();
        $queue->setProject($project);
        $queue->setTask($task);
        $queue->setUser($user);
        $queue->setState(Queue::STATE_QUEUE);
        $queue->setSendToQueue(false);
        $queue->setBranch($project->getBranch());

        return $queue;
    }

    /**
     * @param Queue $queue
     */
    public function save(Queue $queue)
    {
        if (null === $queue->getId()) {
            $this->em->persist($queue);
        }

        $this->em->flush();

        $this->execute($queue);
    }

    /**
     * @param Queue $queue
     *
     * @return bool
     */
    public function execute(Queue $queue)
    {
        if (!$queue->getSendToQueue() && !$this->isQueueOccuped($queue->getProject(), $queue->getEnvironment())) {
            $queue->setSendToQueue(true);
            $this->save($queue);
            $this->producer->publish(serialize([
                'queueId' => $queue->getId(),
            ]));

            return true;
        }

        return false;
    }

    /**
     * @param Project     $project
     * @param Environment $environment
     *
     * @return bool
     */
    public function isQueueOccuped(Project $project, Environment $environment)
    {
        return $this->getRepository()->isQueueOccuped($project, $environment);
    }

    /**
     * @param int $queueId
     *
     * @return Queue
     */
    public function getById($queueId)
    {
        return $this->getRepository()->findOneById($queueId);
    }

    /**
     * @param Project $project
     *
     * @return array
     */
    public function getHistoryByProject(Project $project)
    {
        return $this->getRepository()->getHistoryByProject($project);
    }

    /**
     * @return \AppBundle\Repository\QueueRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('AppBundle:Queue');
    }

    /**
     * Send process signal.
     */
    public function sendProcessSignal()
    {
        $queues = $this->em->getRepository('AppBundle:Queue')->getToSendProcessSignal();
        foreach ($queues as $queue) {
            $this->execute($queue);
        }
    }

    /**
     * @param Project $project
     * @param int     $range
     *
     * @return array
     */
    public function getStatsOfSuccessByDays(Project $project, $range = 7)
    {
        $stats = $this->getRepository()->getStatsByDays($project, $range);
        $data  = [];
        for ($i = 0; $i <= $range; ++$i) {
            $dt     = new \DateTime(sprintf('-%d days', $i));
            $number = 0;
            foreach ($stats as $item) {
                if ($item['label'] === 'success'
                        && $item['day'] == $dt->format('d')) {
                    $number = (int) $item['number'];
                }
            }
            $data[] = $number;
        }

        return array_reverse($data);
    }

    /**
     * @param Project $project
     * @param int     $range
     *
     * @return array
     */
    public function getStatsOfErrorsByDays(Project $project, $range = 7)
    {
        $stats = $this->getRepository()->getStatsByDays($project, $range);
        $data  = [];
        for ($i = 0; $i <= $range; ++$i) {
            $dt     = new \DateTime(sprintf('-%d days', $i));
            $number = 0;
            foreach ($stats as $item) {
                if ($item['label'] === 'error'
                    && $item['day'] == $dt->format('d')) {
                    $number = (int) $item['number'];
                }
            }
            $data[] = $number;
        }

        return array_reverse($data);
    }
}
