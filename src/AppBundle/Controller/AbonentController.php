<?php

namespace AppBundle\Controller;

use AppBundle\Service\QueryModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AbonentController extends DefaultController
{
    /**
     * @Route("/show-transactions", name="abonent_show_transactions")
     */
    public function showTransactionsAction()
    {
        if (!$this->isLoged()) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $qm = $this->container->get('app.query_model');
        $data = [];

        if (null !== $userID = $this->get('session')->get('abonId')) {
            $data = $qm->getTransactionsData($userID);

            if( empty($data) ) {
                return $this->render('abonent/show-transactions.html.twig', array(
                    'transactionData' => $data,
                    'errorMessage' => 'По данному абоненту транзакций не найдено'
                ));
            }
        }

        return $this->render('abonent/show-transactions.html.twig', array(
            'transactionData' => $data,
        ));
    }

    /**
     * @Route("/show-detalization", name="abonent_show_detalization")
     */
    public function showDetalizationAction(Request $request)
    {
        if (!$this->isLoged()) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        $qm = $this->container->get('app.query_model');
        $data = [];

        if (null !== $userID = $this->get('session')->get('abonId')) {
            $data = $qm->getDetalizationData($userID);

            if( empty($data) ) {
                return $this->render('abonent/detalization.html.twig', array(
                    'detalizationData' => $data,
                    'errorMessage' => 'По данному абоненту детализации не найдено'
                ));
            }
        }

        return $this->render('abonent/detalization.html.twig', array(
            'detalizationData' => $data,
        ));
    }

    /**
     * @Route("/personification", name="abonent_personification")
     */
    public function abonPersonificationAction (Request $request)
    {
        if (!$this->isLoged()) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        if (! $userID = $this->get('session')->get('abonId')) {
            $this->addFlash(
                'notice',
                'Сначала выберите абонента для регистрации!'
            );

            return $this->redirectToRoute('homepage');
        }

        $qm = $this->container->get('app.query_model');

        if ($request->request->get('submit'))
        {
            $data = $request->request->all();
            $data['subs_id'] = $userID;

            $qm->userPersonificationData($data);

            $this->addFlash(
                'success',
                'Регистрационные данные пользователя обновлены!'
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render('abonent/personification.html.twig', array(
            'personificationData' => $qm->getPersonificationData($userID),
        ));
    }
}