<?php

namespace AppBundle\Services\Queue;

use AppBundle\Entity\Project;
use AppBundle\Entity\Queue;
use AppBundle\Manager\QueueManager;
use AppBundle\Services\ProjectContext;
use AppBundle\Services\SlackClient;

/**
 * Class QueueRunner.
 */
class QueueRunner
{
    /**
     * @var ProjectContext
     */
    private $projectContext;

    /**
     * @var QueueManager
     */
    private $queueManager;

    /**
     * @var SlackClient
     */
    private $slackClient;

    /**
     * @param ProjectContext $projectContext
     * @param QueueManager   $queueManager
     * @param SlackClient    $slackClient
     */
    public function __construct(ProjectContext $projectContext, QueueManager $queueManager, SlackClient $slackClient)
    {
        $this->projectContext = $projectContext;
        $this->queueManager   = $queueManager;
        $this->slackClient    = $slackClient;
    }

    public function run($queueId)
    {
        $output = null;

        $queue = $this->queueManager->getById($queueId);
        if (!$queue) {
            return $output;
        }

        $queue->setState(Queue::STATE_IN_PROGRESS);
        $this->queueManager->save($queue);
        $this->onStart($queue);

        $error = null;
        try {
            $output = $this->projectContext->run($queue);
            $queue->setState(Queue::STATE_SUCCESS);
            $queue->setOutput($output);
        } catch (\Exception $e) {
            $error  = $e;
            $output = $e->getMessage();
            $queue->setState(Queue::STATE_ERROR);
            $queue->setErrorOutput($output);
        } finally {
            $queue->setIsProcessed(true);
            $this->queueManager->save($queue);
            $this->onComplete($queue);
        }

        if ($e instanceof \Exception) {
            throw $e;
        }
    }

    /**
     * @param Queue $queue
     */
    public function onStart(Queue $queue)
    {
        $project = $queue->getProject();
        if ($queue->getState() === Queue::STATE_IN_PROGRESS) {
            $this->sendSlackIncomingHook(
                $project,
                sprintf('[%s] run task #%d on %s for project %s.',
                    $queue->getTask()->getName(),
                    $queue->getId(),
                    $queue->getEnvironment()->getName(),
                    $queue->getProject()->getName()
                )
            );
        }
    }

    /**
     * @param Queue $queue
     */
    public function onComplete(Queue $queue)
    {
        $project = $queue->getProject();
        if ($queue->getState() === Queue::STATE_SUCCESS) {
            $this->sendSlackIncomingHook(
                $project,
                sprintf('[%s][success] execution success of task #%d on %s for project %s.',
                    $queue->getTask()->getName(),
                    $queue->getId(),
                    $queue->getEnvironment()->getName(),
                    $queue->getProject()->getName()
                )
            );
        } elseif ($queue->getState() === Queue::STATE_ERROR) {
            $this->sendSlackIncomingHook(
                $project,
                sprintf('[%s][fail] execution error of task #%d on %s for project %s. Check logs..',
                    $queue->getTask()->getName(),
                    $queue->getId(),
                    $queue->getEnvironment()->getName(),
                    $queue->getProject()->getName()
                )
            );
        }
    }

    /**
     * @param Project $project
     * @param string  $message
     */
    private function sendSlackIncomingHook(Project $project, $message)
    {
        if (!$project->getSlacks()->count()) {
            return;
        }

        foreach ($project->getSlacks() as $slack) {
            if ($slack->getIsEnabled()) {
                $this->slackClient->send($slack->getWebhookUrl(), $message);
            }
        }
    }
}
