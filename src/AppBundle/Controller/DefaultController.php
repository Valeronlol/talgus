<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    private $qm;

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

        $submit = $request->request->get('submit');
        $phone = $request->request->get('phone');
        $sim = $request->request->get('sim');

        if ($submit) {
            $this->qm = $this->container->get('app.query_model');

            if (null !== $data = $this->getAbonData($phone, $sim)) {
                $this->get('session')->set('abonId', $data['subs_id']);

                return $this->render('pages/main.html.twig', ['userdata' => $data,]);
            } else {
                $this->addFlash(
                    'notice',
                    'По данному запросу ничего не найдено!'
                );
            }

        }
        return $this->render('pages/main.html.twig');
    }

    /**
     * returns array of userdata or null
     * @param $phone
     * @param $sim
     * @return array
     */
    protected function getAbonData ($phone, $sim)
    {
        if (empty($sim) && empty($phone)) {
            $this->addFlash(
                'error',
                'Введите номер абонента или сим карты!'
            );
        }

        if (!empty($sim) && !empty($phone)) {
            $this->addFlash(
                'notice',
                'Нельзя искать одновременно по сим и по телефону!'
            );
            return null;
        }

        // find by phone
        if ($phone && strlen($phone) === 12) {
            return $this->qm->getAbonByPhone($phone);
        } elseif(!empty($phone && strlen($phone) !== 12)) {
            $this->addFlash(
                'error',
                'Номер абонента должен состоять из 12 символов!'
            );
        }

        // find by sim
        if ($sim && strlen($sim) === 15) {
            return $this->qm->getAbonBySim($sim);
        } elseif(!empty($sim && strlen($sim) !== 15)) {
            $this->addFlash(
                'error',
                'Номер sim карты должен состоять из 15 символов!'
            );
        }
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

    /**
     * @Route("/service-config", name="service_configuration")
     */
    public function serviceConfigAction(Request $request)
    {
        if (!$this->isLoged()) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        return $this->render('pages/service-config.html.twig');
    }
}
