<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Collaborator;
use AppBundle\Entity\Project;
use AppBundle\Form\Type\CollaboratorType;
use AppBundle\Form\Type\FastRegisterType;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Oneup\AclBundle\Configuration\ParamPermission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Class CollaboratorController.
 */
class CollaboratorController extends Controller
{
    /**
     * @Route("/collaborators/{id}", name="app_projects_collaborators")
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Project $project)
    {
        $collaborators = $this->getCollaboratorManager()->getByProject($project);

        $manager             = $this->get('oneup_acl.manager');
        $hasPermissionDelete = $manager->isGranted('OWNER', $project);

        return $this->render('AppBundle:Collaborator:list.html.twig', [
            'list'                => $collaborators,
            'project'             => $project,
            'hasPermissionDelete' => $hasPermissionDelete,
        ]);
    }

    /**
     * @Route("/collaborators/{id}/add", name="app_projects_collaborators_add")
     * @ParamPermission({ "project" = "VIEW" })
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, Project $project)
    {
        $aclManager  = $this->get('oneup_acl.manager');
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher  = $this->get('event_dispatcher');
        $user        = $userManager->createUser();
        $password    = uniqid();
        $user->setPlainPassword($password);
        $user->setLocale($request->getLocale());

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        $formNewUser = $this->createForm(new FastRegisterType(), $user);
        $form        = $this->createForm(new CollaboratorType(), $this->getCollaboratorManager()->create($project), [
            'entity_manager' => $this->getDoctrine()->getManager(),
        ]);
        $form->handleRequest($request);
        $formNewUser->handleRequest($request);

        if ($isValidNewUser = $formNewUser->isValid()) {
            $event = new FormEvent($formNewUser, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url      = $this->generateUrl('fos_user_registration_confirmed');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            $collaborator = $this->getCollaboratorManager()->create($project);
            $collaborator->setUser($user);
            $this->getCollaboratorManager()->save($collaborator);

            $aclManager->setObjectPermission($form->getData()->getProject(), MaskBuilder::MASK_VIEW, $formNewUser->getData());
            $aclManager->addObjectPermission($form->getData()->getProject(), MaskBuilder::MASK_EDIT, $formNewUser->getData());

            $this->get('app.mailer')->sendNewCollaborator($user, $project, $password);

            $this->addFlash('success', 'The account of collaborator was created and an email has been send to the collaborator.');

            return $this->redirectToRoute('app_projects_collaborators_add', ['id' => $project->getId()]);
        }

        if ($isValid = $form->isValid()) {
            if ($form->getData()->getUser()->getId() !== $this->getUser()->getId()) {
                $this->getCollaboratorManager()->save($form->getData());

                $aclManager->setObjectPermission($form->getData()->getProject(), MaskBuilder::MASK_VIEW, $form->getData()->getUser());
                $aclManager->addObjectPermission($form->getData()->getProject(), MaskBuilder::MASK_EDIT, $form->getData()->getUser());

                $this->addFlash('success', 'The collaborator was successfull added.');

                return $this->redirectToRoute('app_projects_collaborators', ['id' => $project->getId()]);
            } else {
                $this->addFlash('danger', 'Can\'t add yourself to collaborators  .');

                return $this->redirectToRoute('app_projects_collaborators_add', ['id' => $project->getId()]);
            }
        }

        return $this->render('AppBundle:Collaborator:add.html.twig', [
            'form'           => $form->createView(),
            'formNewUser'    => $formNewUser->createView(),
            'project'        => $project,
            'isValid'        => $isValid,
            'isValidNewUser' => $isValidNewUser,
        ]);
    }

    /**
     * @Route("/collaborators/{id}/delete", name="app_projects_collaborators_delete")
     * @Method("POST")
     *
     * @param  Request      $request
     * @param  Collaborator $collaborator
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Collaborator $collaborator)
    {
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException();
        }

        $project = $collaborator->getProject();
        $user    = $collaborator->getUser();
        $manager = $this->get('oneup_acl.manager');

        if ($manager->isGranted('OWNER', $project)) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($collaborator);
            $em->flush();
            $manager->revokeObjectPermissions($project, $user);
        }

        return new JsonResponse(true);
    }

    /**
     * @return \AppBundle\Manager\CollaboratorManager
     */
    private function getCollaboratorManager()
    {
        return $this->get('app.collaborator_manager');
    }
}
