<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use Oneup\AclBundle\Configuration\ParamPermission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class TaskController.
 */
class TaskController extends Controller
{
    /**
     * @Route("/tasks/{id}", name="app_project_tasks")
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Project $project)
    {
        $tasks = $this->getTaskManager()->getByProject($project);

        return $this->render('AppBundle:Task:list.html.twig', [
            'list'    => $tasks,
            'project' => $project,
        ]);
    }

    /**
     * @Route("/tasks/{id}/update", name="app_project_tasks_update")
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Project $project)
    {
        $this->getTaskManager()->sendRetrieveList($project);

        return $this->render('AppBundle:Task:_update.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @return \AppBundle\Manager\TaskManager
     */
    private function getTaskManager()
    {
        return $this->get('app.task_manager');
    }
}
