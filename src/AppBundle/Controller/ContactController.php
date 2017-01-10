<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ContactController.
 */
class ContactController extends Controller
{
    /**
     * @Route("/contact", name="app_contact")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(new ContactType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('app.mailer')->sendContactAdmin(
                $form->getData()->getMessage(),
                $form->getData()->getEmail(),
                $form->getData()->getSubject()
            );
            $this->addFlash('success', $this->get('translator')->trans('contact.success', [], 'flash'));

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('AppBundle:Contact:form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
