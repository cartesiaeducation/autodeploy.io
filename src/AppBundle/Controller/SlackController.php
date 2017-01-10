<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\Slack;
use AppBundle\Form\Type\SlackType;
use Oneup\AclBundle\Configuration\ParamPermission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SlackController.
 */
class SlackController extends Controller
{
    /**
     * @Route("/slacks/{id}", name="app_project_slacks")
     * @ParamPermission({ "project" = "VIEW" })
     */
    public function listAction(Project $project)
    {
        $environments = $this->getSlackManager()->getByProject($project);

        return $this->render('AppBundle:Slack:list.html.twig', [
            'list'    => $environments,
            'project' => $project,
        ]);
    }

    /**
     * @Route("/slacks/{id}/add", name="app_projects_slacks_add")
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, Project $project)
    {
        $form = $this->createForm(new SlackType(), $this->getSlackManager()->create($project));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getSlackManager()->save($form->getData());

            $this->addFlash('success', 'The slack webhook was successfull added.');

            return $this->redirectToRoute('app_project_slacks', ['id' => $project->getId()]);
        }

        return $this->render('AppBundle:Slack:add.html.twig', [
            'form'    => $form->createView(),
            'project' => $project,
        ]);
    }

    /**
     * @Route("/slacks/{id}/edit", name="app_projects_slacks_edit")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Slack $slack)
    {
        $project = $slack->getProject();
        $manager = $this->get('oneup_acl.manager');

        if (!$manager->isGranted('VIEW', $project)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(new SlackType(), $slack);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getSlackManager()->save($form->getData());

            $this->addFlash('success', 'The slack webhook was successfull updated.');

            return $this->redirectToRoute('app_project_slacks', ['id' => $project->getId()]);
        }

        return $this->render('AppBundle:Slack:edit.html.twig', [
            'form'    => $form->createView(),
            'project' => $project,
            'slack'   => $slack,
        ]);
    }

    /**
     * @Route("/slacks/{id}/delete", name="app_projects_slacks_delete")
     * @Method("POST")
     *
     * @param Request $request
     * @param Slack   $slack
     
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Slack $slack)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $project = $slack->getProject();
        $manager = $this->get('oneup_acl.manager');
        if ($manager->isGranted('VIEW', $project)) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($slack);
            $em->flush();
        }

        return new JsonResponse(true);
    }

    /**
     * @return SlackManager
     */
    private function getSlackManager()
    {
        return $this->get('app.slack_manager');
    }
}
