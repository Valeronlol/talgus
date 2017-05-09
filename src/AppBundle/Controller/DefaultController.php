<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * Check is user authorizated
     * @return bool
     */
    protected function isLoged()
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            return false;
        }
        return true;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        if (!$this->isLoged()) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        return $this->render('pages/main.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }

    /**
     * @Route("/ua-search", name="ua-search")
     */
    public function uaAction(Request $request)
    {
        if (!$this->isLoged()) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        return $this->render('pages/ua.html.twig');
    }

    /**
     * @Route("/statistics", name="statistics")
     */
    public function statisticsAction(Request $request)
    {
        if (!$this->isLoged()) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        return $this->render('pages/ua.html.twig');
    }
}
