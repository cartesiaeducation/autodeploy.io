<?php

namespace AppBundle\Services\Capistrano;

use AppBundle\Entity\Project;
use AppBundle\Entity\Task;
use AppBundle\Manager\ProjectManager;
use AppBundle\Services\ProjectContext;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * Class CapistranoTask.
 */
class CapistranoTask
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var Producer
     */
    protected $producer;

    /**
     * @var ProjectManager
     */
    protected $projectManager;

    /**
     * @var ProjectContext
     */
    protected $projectContext;

    /**
     * @param EntityManagerInterface $em
     * @param Producer               $producer
     * @param ProjectManager         $projectManager
     * @param ProjectContext         $projectContext
     */
    public function __construct(EntityManagerInterface $em, Producer $producer, ProjectManager $projectManager, ProjectContext $projectContext)
    {
        $this->em             = $em;
        $this->producer       = $producer;
        $this->projectManager = $projectManager;
        $this->projectContext = $projectContext;
    }

    /**
     * @param Project $project
     */
    public function sendToQueue(Project $project)
    {
        if (!$project->isTaskRetrieveQueue()) {
            if ($project->hasValidAuthentificationRepository()) {
                $this->onProgress($project);

                $this->producer->publish(serialize([
                    'projectId' => $project->getId(),
                ]));
            } else {
                $project->setTaskRetrieveStatus(Project::TASK_STATUS_ERROR_AUTH);
                $this->em->flush();
            }
        }
    }

    /**
     * @param Project $project
     *
     * @throws \Exception
     */
    public function retrieve(Project $project)
    {
        $this->onInit($project);
        $this->onProgress($project);

        try {
            $tasks = $this->projectContext->getTasks($project);
            foreach ($tasks as $task) {
                $this->createTask($project, $task);
            }
            $this->onSuccess($project);
        } catch (\Exception $e) {
            $this->onError($project, $e);
            throw $e;
        }
    }

    /**
     * @param Project $project
     */
    protected function onInit(Project $project)
    {
        $project->disableTasks();
    }

    /**
     * @param Project $project
     */
    protected function onProgress(Project $project)
    {
        $project->setTaskRetrieveStatus(Project::TASK_STATUS_PROGRESS);
        $this->em->flush();
    }

    /**
     * @param Project $project
     */
    protected function onSuccess(Project $project)
    {
        $project->setTaskRetrieveStatus(Project::TASK_STATUS_SUCCESS);
        $this->em->flush();
    }

    /**
     * @param Project    $project
     * @param \Exception $e
     */
    protected function onError(Project $project, \Exception $e)
    {
        switch ($e->getMessage()) {
            case 'Capfile missing in root project directory':
                $project->setTaskRetrieveStatus(Project::TASK_STATUS_ERROR_MISSING_CAP);
                break;

            default:
                $project->setTaskRetrieveStatus(Project::TASK_STATUS_ERROR);
                break;
        }

        $this->em->flush();
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isPriorityTask($name)
    {
        switch ($name) {
            case 'deploy':
            case 'npm:install':
            case 'bower:install':
            case 'composer:install':
            case 'php5:reload':
            case 'grunt':
            case 'doctrine:migrate':
            case 'symfony:cache:clear':
                return true;

            default:
                return false;
        }
    }

    /**
     * @param Project $project
     * @param array   $data
     *
     * @return Task
     */
    protected function createTask(Project $project, array $data)
    {
        if (!$task = $this->em->getRepository('AppBundle:Task')->getTaskByName($project, $data['name'])) {
            $task = new Task();
        }

        $task->setProject($project);
        $task->setName($data['name']);
        $task->setCommand($data['command']);
        $task->setDescription($data['description']);
        $task->setIsEnabled(true);
        $task->setHasPriority($this->isPriorityTask($data['name']));

        $this->em->persist($task);

        return $task;
    }
}
