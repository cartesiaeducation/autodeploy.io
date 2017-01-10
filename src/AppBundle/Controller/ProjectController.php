<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Form\Type\ProjectType;
use Oneup\AclBundle\Configuration\ParamPermission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Class ProjectController.
 */
class ProjectController extends Controller
{
    /**
     * @Route("/projects/", name="app_projects")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $projects = $this->getProjectManager()->findAll($this->getUser());

        return $this->render('AppBundle:Project:list.html.twig', [
            'list' => $projects,
        ]);
    }

    /**
     * @Route("/projects/add", name="app_projects_add")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(new ProjectType(), $this->getProjectManager()->create($this->getUser()));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getProjectManager()->save($form->getData());

            $manager = $this->get('oneup_acl.manager');
            $manager->addObjectPermission($form->getData(), MaskBuilder::MASK_OWNER);
            $manager->addObjectPermission($form->getData(), MaskBuilder::MASK_VIEW);
            $manager->addObjectPermission($form->getData(), MaskBuilder::MASK_EDIT);

            $this->addFlash('success', 'The project was successfull added.');

            return $this->redirectToRoute('app_projects_show', ['id' => $form->getData()->getId()]);
        }

        return $this->render('AppBundle:Project:add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/projects/edit/{id}", name="app_projects_edit")
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Project $project)
    {
        $aclManager = $this->get('oneup_acl.manager');
        $form       = $this->createForm(new ProjectType(), $project);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getProjectManager()->save($form->getData());

            $this->addFlash('success', 'The project was successfull updated.');

            return $this->redirectToRoute('app_projects_edit', ['id' => $project->getId()]);
        }

        return $this->render('AppBundle:Project:edit.html.twig', [
            'form'    => $form->createView(),
            'project' => $project,
            'isOwner' => $aclManager->isGranted('OWNER', $project),
        ]);
    }

    /**
     * @Route("/projects/{id}/delete", name="app_projects_delete")
     *
     * @param Project $project
     *
     * @return Response
     */
    public function deleteAction(Project $project)
    {
        $manager = $this->get('oneup_acl.manager');

        if (!$manager->isGranted('OWNER', $project)) {
            throw $this->createAccessDeniedException();
        }

        $this->get('app.project_manager')->delete($project);

        $this->addFlash('success', 'Project deleted.');

        return $this->redirectToRoute('app_projects');
    }

    /**
     * @Route("/projects/show/{id}/check-permissions", name="app_projects_check_permissions")
     * @Method({"POST"})
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkPermissionsAction(Project $project)
    {
        $this->getProjectManager()->checkPermissions($project);

        return new Response();
    }

    /**
     * @Route("/projects/show/{id}/state-permissions", name="app_projects_state_permissions")
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @param Project $project
     *
     * @return JsonResponse
     */
    public function statePermissionsAction(Project $project)
    {
        return new JsonResponse($project->hasValidAuthentificationRepository());
    }

    /**
     * @Route("/projects/show/{id}", name="app_projects_show")
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Project $project)
    {
        $days    = $this->get('app.chart')->generateDayPeriod(7);
        $success = $this->get('app.queue_manager')->getStatsOfSuccessByDays($project, 7);
        $error   = $this->get('app.queue_manager')->getStatsOfErrorsByDays($project, 7);

        return $this->render('AppBundle:Project:show.html.twig', [
            'project' => $project,
            'days'    => json_encode($days),
            'error'   => json_encode($error),
            'success' => json_encode($success),
        ]);
    }

    /**
     * @return \AppBundle\Manager\ProjectManager
     */
    private function getProjectManager()
    {
        return $this->get('app.project_manager');
    }
}
