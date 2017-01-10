<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Form\Type\AuthentificationType;
use Oneup\AclBundle\Configuration\ParamPermission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthentificationController.
 */
class AuthentificationController extends Controller
{
    /**
     * @Route("/authentifications/{id}", name="app_project_authentification_list")
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @param Project $project
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Project $project)
    {
        $list = $this->getAuthentificationManager()->findAll($project, true);
        if (!count($list)) {
            return $this->forward('AppBundle:Authentification:Add', ['id' => $project->getId()]);
        }

        return $this->render('AppBundle:Authentification:list.html.twig', [
            'project' => $project,
            'list'    => $list,
        ]);
    }

    /**
     * @Route("/authentifications/{id}/add", name="app_project_authentification_add")
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @param Request $request
     * @param Project $project
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, Project $project, $type = null)
    {
        $form = $this->createForm(new AuthentificationType(), $this->getAuthentificationManager()->create($project, $this->getUser(), $type), [
            'show_type' => $type === null && !$request->isXmlHttpRequest(),
        ]);
        $form->handleRequest($request);

        if ($isValid = $form->isValid()) {
            $this->getAuthentificationManager()->save($form->getData());

            $this->addFlash('success', 'The SSK key was successfull added.');

            return $this->redirectToRoute('app_project_authentification_list', ['id' => $project->getId()]);
        }

        $response = null;
        if ($request->isXmlHttpRequest() && !$isValid) {
            $response = new Response('', 422);
        }

        return $this->render('AppBundle:Authentification:add.html.twig', [
            'layout'  => $type !== null || $request->isXmlHttpRequest() ? 'AppBundle:Project:layout_ajax.html.twig' : 'AppBundle:Project:layout.html.twig',
            'form'    => $form->createView(),
            'project' => $project,
        ], $response);
    }

    /**
     * @Route("/authentifications/{id}/regenerate", name="app_project_repository_regenerate")
     * @Method({"GET"})
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @param Project $project
     *
     * @return Response
     */
    public function regenerateAction(Request $request, Project $project)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        if ($project->hasAuthentificationRepository()) {
            $this->getAuthentificationManager()->delete($project->getAuthentificationRepository());
        }

        $projectManager   = $this->get('app.project_manager');
        $authentification = $projectManager->createDefaultAuthentificationRepository($project);
        $project->addAuthentification($authentification);
        $projectManager->save($project);

        $this->getDoctrine()->getEntityManager()->refresh($authentification);

        return $this->render('AppBundle:Authentification:regenerate.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @return \AppBundle\Manager\ProjectAuthentificationManager
     */
    private function getAuthentificationManager()
    {
        return $this->get('app.project_authentification_manager');
    }
}
