<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\Queue;
use Oneup\AclBundle\Configuration\ParamPermission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class HistoryController.
 */
class HistoryController extends Controller
{
    /**
     * @Route("/history/{id}", name="app_project_history")
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Project $project)
    {
        $histories = $this->getQueueManager()->getHistoryByProject($project);

        return $this->render('AppBundle:History:list.html.twig', [
            'project' => $project,
            'list'    => $histories,
        ]);
    }

    /**
     * @Route("/history/{id}/show", name="app_project_history_show")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Queue $queue)
    {
        return $this->render('AppBundle:History:show.html.twig', [
            'history' => $queue,
            'project' => $queue->getProject(),
        ]);
    }

    /**
     * @return \AppBundle\Manager\QueueManager
     */
    private function getQueueManager()
    {
        return $this->get('app.queue_manager');
    }
}
