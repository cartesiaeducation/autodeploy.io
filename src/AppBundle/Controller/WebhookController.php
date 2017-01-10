<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\Webhook;
use AppBundle\Form\Type\WebhookType;
use Oneup\AclBundle\Configuration\ParamPermission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class WebhookController.
 */
class WebhookController extends Controller
{
    /**
     * @Route("/webhooks/{id}", name="app_project_webhooks")
     * @ParamPermission({ "project" = "VIEW" })
     */
    public function listAction(Project $project)
    {
        $environments = $this->getWebhookManager()->getByProject($project);

        return $this->render('AppBundle:Webhook:list.html.twig', [
            'list'    => $environments,
            'project' => $project,
        ]);
    }

    /**
     * @Route("/webhooks/{id}/add", name="app_projects_webhooks_add")
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, Project $project)
    {
        $form = $this->createForm(new WebhookType(), $this->getWebhookManager()->create($project, $this->getUser()), [
            'project' => $project,
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getWebhookManager()->save($form->getData());

            $this->addFlash('success', 'The webhook was successfull added.');

            return $this->redirectToRoute('app_project_webhooks', ['id' => $project->getId()]);
        }

        return $this->render('AppBundle:Webhook:add.html.twig', [
            'form'    => $form->createView(),
            'project' => $project,
        ]);
    }

    /**
     * @Route("/webhooks/{id}/edit", name="app_projects_webhooks_edit")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Webhook $webhook)
    {
        $project = $webhook->getProject();
        $manager = $this->get('oneup_acl.manager');

        if (!$manager->isGranted('VIEW', $project)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(new WebhookType(), $webhook, [
            'project' => $project,
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getWebhookManager()->save($form->getData());

            $this->addFlash('success', 'The webhook was successfull updated.');

            return $this->redirectToRoute('app_project_webhooks', ['id' => $project->getId()]);
        }

        return $this->render('AppBundle:Webhook:edit.html.twig', [
            'form'    => $form->createView(),
            'project' => $project,
            'webhook' => $webhook,
        ]);
    }

    /**
     * @Route("/webhooks/{id}/delete", name="app_projects_webhooks_delete")
     * @Method("POST")
     *
     * @param Request $request
     * @param Webhook $webhook
     
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Webhook $webhook)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $project = $webhook->getProject();
        $manager = $this->get('oneup_acl.manager');
        if ($manager->isGranted('VIEW', $project)) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($webhook);
            $em->flush();
        }

        return new JsonResponse(true);
    }

    /**
     * @Route("/webhook-process/{token}", name="app_projects_webhooks_process")
     * @Method("POST")
     
     * @return JsonResponse
     */
    public function processAction($token)
    {
        $webhook = $this->getWebhookManager()->getByToken($token);
        if (!$webhook) {
            throw $this->createNotFoundException(sprintf('Webhook with token "%s" not found', $token));
        }

        $queue = $this->getQueueManager()->create($webhook->getProject(), $webhook->getUser(), $webhook->getTask());
        $queue->setEnvironment($webhook->getEnvironment());
        $this->getQueueManager()->save($queue);

        return new JsonResponse(true);
    }

    /**
     * @return WebhookManager
     */
    private function getWebhookManager()
    {
        return $this->get('app.webhook_manager');
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
