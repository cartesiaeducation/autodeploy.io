<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class PageController.
 *
 * @Cache(smaxage="3600", maxage="3600", public=true)
 */
class PageController extends Controller
{
    /**
     * @Route("/features", name="app_features")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function featuresAction()
    {
        return $this->render('AppBundle:Page:features.html.twig');
    }

    /**
     * @Route("/pricing", name="app_pricing")
     * @Cache(public=false)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pricingAction()
    {
        return $this->render('AppBundle:Page:pricing.html.twig');
    }

    /**
     * @Route("/legal", name="app_legal")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function legalAction()
    {
        return $this->render('AppBundle:Page:legal.html.twig');
    }

    /**
     * @Route("/docs", name="app_documentations")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function docsAction()
    {
        return $this->render('AppBundle:Page:docs.html.twig');
    }

    /**
     * @Route("/docs/cookbook-symfony2-capistrano", name="app_doc_symfony2")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function symfonyAction()
    {
        return $this->render('AppBundle:Page:doc_symfony.html.twig');
    }

    /**
     * @Route("/docs/create-new-project", name="app_doc_new_project")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function docNewProjectAction()
    {
        return $this->render('AppBundle:Page:doc_new_project.html.twig');
    }

    /**
     * @Route("/docs/git-authentification", name="app_doc_git_authentification")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function docGitAuthProjectAction()
    {
        return $this->render('AppBundle:Page:doc_git_auth.html.twig');
    }
}
