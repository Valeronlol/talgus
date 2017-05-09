<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends DefaultController
{
    private $qm;

    /**
     * @Route("/user-show/{userID}", name="user_show")
     */
    public function userShowAction($userID, Request $request)
    {
        $this->qm = $this->container->get('app.query_model');
        $userdata = [];

        $userdata['abonent'] = $this->qm->getUserStatisticsAbonentById($userID);
        $userdata['base'] = $this->qm->getUserStatisticsBaseServicesById($userID);
        $userdata['additional'] = $this->qm->getUserStatisticsAdditionalServiceById($userID);

        return $this->render('user/show.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'userdata' => $userdata,
        ));
    }
}