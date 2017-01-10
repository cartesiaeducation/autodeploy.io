<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\Capistrano\WizardType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WizardController.
 */
class WizardController extends Controller
{
    /**
     * @Route("/capistrano-wizard", name="app_capistrano_wizard")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(new WizardType(), $this->getCapistranoWizard()->create());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $archive = $this->getCapistranoWizard()->createArchive($form->getData());

            $response = new Response();
            $response->headers->set('Content-type', 'application/octet-stream');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . uniqid('capistrano-config') . '.zip"');
            $response->setContent(file_get_contents($archive));

            return $response;
        }

        return $this->render('AppBundle:Wizard:index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return \AppBundle\Services\Capistrano\CapistranoWizard
     */
    protected function getCapistranoWizard()
    {
        return $this->get('app.caspitrano_wizard');
    }
}
