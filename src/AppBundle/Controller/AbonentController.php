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
        $qm = $this->container->get('app.query_model');
        $data = [];

        if (null !== $userID = $this->get('session')->get('abonId')) {
            $data = $qm->getDetalizationData($userID);
        }

        return $this->render('abonent/detalization.html.twig', array(
            'detalizationData' => $data,
        ));
    }
}