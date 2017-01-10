<?php

namespace AppBundle\Services;

use AppBundle\Entity\Project;
use AppBundle\Entity\Queue;
use AppBundle\Services\Capistrano\CapistranoManager;
use AppBundle\Services\Git\GitManager;

/**
 * Class ProjectContext.
 */
class ProjectContext
{
    /**
     * @var GitManager
     */
    private $gitManager;

    /**
     * @var CapistranoManager
     */
    private $capistranoManager;

    /**
     * @param GitManager        $gitManager
     * @param CapistranoManager $capistranoManager
     */
    public function __construct(GitManager $gitManager, CapistranoManager $capistranoManager)
    {
        $this->gitManager        = $gitManager;
        $this->capistranoManager = $capistranoManager;
    }

    /**
     * @param Project    $project
     * @param Queue|null $queue
     *
     * @return string
     *
     * @throws \Exception
     */
    private function setup(Project $project, Queue $queue = null)
    {
        $branch = $project->getBranch();
        if (null !== $queue) {
            $branch = $queue->getBranch();
        }

        $localPath = $this->gitManager->cloneRepository($project, $branch);
        $this->capistranoManager->setup($localPath);
        $this->capistranoManager->install($localPath);

        return $localPath;
    }

    /**
     * @param Project $project
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getTasks(Project $project)
    {
        return $this->capistranoManager->getTasks($this->setup($project));
    }

    /**
     * @param Queue $queue
     *
     * @return bool
     */
    public function run(Queue $queue)
    {
        return $this->capistranoManager->run(
            $queue->getTask()->getCommand(),
            $queue->getEnvironment()->getName(),
            $this->setup($queue->getProject(), $queue),
            $queue->getProject()->getAuthentificationRepository(),
            $queue->getEnvironment()->getAuthentification(),
            $queue->getId()
        );
    }
}
