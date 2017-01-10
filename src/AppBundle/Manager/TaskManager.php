<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Project;
use AppBundle\Entity\Task;
use AppBundle\Services\Capistrano\CapistranoTask;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TaskManager.
 */
class TaskManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ProjectManager
     */
    protected $projectManager;

    /**
     * @var CapistranoTask
     */
    protected $capistranoTask;

    /**
     * @param EntityManagerInterface $em
     * @param ProjectManager         $projectManager
     * @param CapistranoTask         $capistranoTask
     */
    public function __construct(EntityManagerInterface $em, ProjectManager $projectManager, CapistranoTask $capistranoTask)
    {
        $this->em             = $em;
        $this->capistranoTask = $capistranoTask;
        $this->projectManager = $projectManager;
    }

    /**
     * Get repository.
     *
     * @return \AppBundle\Repository\TaskRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('AppBundle:Task');
    }

    /**
     * @param Project $project
     *
     * @return array
     */
    public function getByProject(Project $project)
    {
        return $this->getRepository()->getByProject($project);
    }

    /**
     * Update task availables for a project.
     *
     * @param Project $project
     */
    public function sendRetrieveList(Project $project)
    {
        $this->capistranoTask->sendToQueue($project);
    }

    /**
     * @param $projectId
     *
     * @throws \Exception
     */
    public function retrieveList($projectId)
    {
        $project = $this->projectManager->findOneById($projectId);
        if (!$project) {
            throw new \Exception('Cant retrieve project entity');
        }

        $this->capistranoTask->retrieve($project);
    }
}
