<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Environment;
use AppBundle\Entity\Project;
use AppBundle\Form\Type\EnvironmentType;
use Oneup\AclBundle\Configuration\ParamPermission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EnvironmentController.
 */
class EnvironmentController extends Controller
{
    /**
     * @Route("/environments/{id}", name="app_project_environments")
     * @ParamPermission({ "project" = "VIEW" })
     */
    public function listAction(Project $project)
    {
        $environments = $this->getEnvironmentManager()->getByProject($project);

        return $this->render('AppBundle:Environment:list.html.twig', [
            'list'    => $environments,
            'project' => $project,
        ]);
    }

    /**
     * @Route("/environments/{id}/add", name="app_projects_environments_add")
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, Project $project)
    {
        $form = $this->createForm(new EnvironmentType(), $this->getEnvironmentManager()->create($project));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getEnvironmentManager()->save($form->getData());

            $this->addFlash('success', 'The environment was successfull added.');

            return $this->redirectToRoute('app_project_environments', ['id' => $project->getId()]);
        }

        return $this->render('AppBundle:Environment:add.html.twig', [
            'form'    => $form->createView(),
            'project' => $project,
        ]);
    }

    /**
     * @Route("/environments/{id}/edit", name="app_projects_environments_edit")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Environment $environment)
    {
        $project = $environment->getProject();
        $manager = $this->get('oneup_acl.manager');

        if (!$manager->isGranted('VIEW', $project)) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(new EnvironmentType(), $environment);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getEnvironmentManager()->save($form->getData());

            $this->addFlash('success', 'The environment was successfull updated.');

            return $this->redirectToRoute('app_project_environments', ['id' => $project->getId()]);
        }

        return $this->render('AppBundle:Environment:edit.html.twig', [
            'form'        => $form->createView(),
            'project'     => $project,
            'environment' => $environment,
        ]);
    }

    /**
     * @Route("/environments/{id}/regenerate", name="app_projects_environments_regenerate")
     *
     * @param Environment $environment
     *
     * @return Response
     */
    public function regenerateAction(Environment $environment)
    {
        $project = $environment->getProject();
        $manager = $this->get('oneup_acl.manager');

        if (!$manager->isGranted('VIEW', $project)) {
            throw $this->createAccessDeniedException();
        }

        if ($authentification = $environment->getAuthentification()) {
            $newKey = $this->get('app.ssh_manager')->generateKey();
            $authentification->setSsh($newKey['privatekey']);
            $authentification->setSshPublic($newKey['publickey']);

            $this->getDoctrine()->getEntityManager()->flush();
        }

        $this->addFlash('success', 'New public key generated.');

        return $this->redirectToRoute('app_project_environments', ['id' => $project->getId()]);
    }

    /**
     * @Route("/environments/{id}/delete", name="app_project_environments_delete")
     *
     * @param Environment $environment
     *
     * @return Response
     */
    public function deleteAction(Environment $environment)
    {
        $project = $environment->getProject();
        $manager = $this->get('oneup_acl.manager');

        if (!$manager->isGranted('VIEW', $project)) {
            throw $this->createAccessDeniedException();
        }

        $this->get('app.environment_manager')->delete($environment);

        $this->addFlash('success', 'Environement deleted.');

        return $this->redirectToRoute('app_project_environments', ['id' => $project->getId()]);
    }

    /**
     * @return EnvironmentManager
     */
    private function getEnvironmentManager()
    {
        return $this->get('app.environment_manager');
    }
}
