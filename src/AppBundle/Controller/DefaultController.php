<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        return $this->render('pages/main.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }
//
//    /**
//     * @Route("/login", name="login")
//     */
//    public function loginAction(Request $request)
//    {
//
//        return $this->render('auth/login.html.twig');
//    }
//
    /**
     * @Route("/ua-search", name="ua-search")
     */
    public function uaAction(Request $request)
    {

        return $this->render('pages/ua.html.twig');
    }
//
//    /**
//     * @Route("/change-password", name="change-password")
//     */
//    public function changePasswordAction(Request $request)
//    {
//
//        return $this->render('auth/change.html.twig');
//    }
//
//    /**
//     * @Route("/create-user", name="create-user")
//     */
//    public function createUserAction(Request $request)
//    {
//
//        return $this->render('user/create.html.twig');
//    }


}
