<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Form\Type\QueueType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class QueueController.
 */
class QueueController extends Controller
{
    /**
     * @Route("/tasks/{id}/run", name="app_project_queue_add")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, Task $task)
    {
        $project = $task->getProject();
        $manager = $this->get('oneup_acl.manager');

        if (!$manager->isGranted('VIEW', $project)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(new QueueType(), $this->getQueueManager()->create($project, $this->getUser(), $task), [
            'project' => $project,
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getQueueManager()->save($form->getData());
            $this->get('metric')->add('app_queue', [
                'value' => 1,
            ], [
                'project'   => $project->getName(),
                'task_name' => $task->getName(),
            ]);

            $this->addFlash('success', sprintf('The task "%s" will be executed in few seconds...', $form->getData()->getTask()->getName()));

            return $this->redirectToRoute('app_project_history_show', ['id' => $form->getData()->getId()]);
        }

        return $this->render('AppBundle:Queue:add.html.twig', [
            'form'    => $form->createView(),
            'project' => $project,
            'task'    => $task,
        ]);
    }

    /**
     * Get queue manager.
     *
     * @return QueueManager
     */
    private function getQueueManager()
    {
        return $this->get('app.queue_manager');
    }
}
