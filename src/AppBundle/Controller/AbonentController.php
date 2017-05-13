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
    public function showTransactionsShowAction()
    {
        $qm = $this->container->get('app.query_model');
        $data = $qm->getTransactionsData();

        return $this->render('transactions/show.html.twig', array(
            'transactionData' => $data,
        ));
    }
}