<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends DefaultController
{
    /**
     * @Route("/user-show/{userID}", name="user_show")
     */
    public function userShowAction($userID)
    {
        $userdata = $this->getUserData($userID);

        return $this->render('user/show.html.twig', array(
            'userdata' => $userdata,
        ));
    }

    /**
     * @Route("/user-edit/{userID}", name="user_edit")
     */
    public function userEditAction($userID)
    {
        $userdata = $this->getUserData($userID);

        return $this->render('user/edit.html.twig', array(
            'userdata' => $userdata,
        ));
    }

    /**
     * @Route("/user-edit-ajax", name="user_edit_change_status")
     */
    public function setBaseStatus(Request $request)
    {
        if ($request->isXMLHttpRequest())
        {
            $qm = $this->container->get('app.query_model');
            $userid = $this->getUserIdByRef($request);
            $service = $request->request->get('service');
            $action = $request->request->get('action') === 'on' ? 1 : 0;

            if ( 1 === $qm->updateBaseServiceStatus($userid, $service, $action) ) {
                return new JsonResponse([
                    'status' => true,
                    'action' => $action,
                    'service' => $service,
                    'userid' => $userid,
                ]);
            } else {
                return new JsonResponse([
                    'status' => false
                ]);
            }
        }

        return new Response('This is not ajax!', 400);
    }

    /**
     * Get user id from referrer string
     * @param Request $request
     * @return mixed
     */
    private function getUserIdByRef(Request $request)
    {
        $referer = $request->headers->get('referer');
        $path = parse_url($referer)['path'];
        preg_match_all('!\d+!', $path, $matches);

        return $matches[0][0];
    }

    /**
     * Get user data by id
     * @param $userID
     * @return array
     */
    private function getUserData($userID)
    {
        $qm = $this->container->get('app.query_model');
        $userdata = [];

        if(null !== $abonent = $qm->getUserStatisticsAbonentById($userID)) {
            $userdata['Абонент'] = $abonent;
        }

        if(null !== $abonent = $base = $qm->getUserStatisticsBaseServicesById($userID)) {
            $userdata['Базовые сервисы'] = $base;
        }

        if(null !== $additional = $additional = $qm->getUserStatisticsAdditionalServiceById($userID)) {
            $userdata['Дополнительные сервисы'] = $additional;
        }

        return $userdata;
    }
}
