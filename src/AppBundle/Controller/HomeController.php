<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class HomeController.
 *
 * @Cache(smaxage="3600", maxage="3600", public=true)
 */
class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction()
    {
        return $this->render('AppBundle:Home:homepage.html.twig');
    }

    /**
     * @Cache(public=false)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loggedAction()
    {
        return $this->render('AppBundle:Home:_logged.html.twig');
    }
}
