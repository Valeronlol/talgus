<?php

namespace AppBundle\Controller;

use AppBundle\Service\QueryModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountController extends Controller
{
    /**
     * @Route("change-user-password", name="change_user_password")
     */
    public function changePasswordAction(Request $request)
    {
        $userName = $request->request->get('change-user');
        $newPass = $request->request->get('new-password');
        $repeatNewPass = $request->request->get('repeat-new-password');
        $submit = $request->request->get('submit');
        $errors = false;

        if($submit)
        {
            if(empty($newPass) || empty($repeatNewPass) || empty($userName)){
                $this->addFlash(
                    'error',
                    'Заполните все поля!'
                );
                $errors = true;
            }

            if ($newPass !== $repeatNewPass) {
                $this->addFlash(
                    'error',
                    'Пароли должны совпадать!'
                );
                $errors = true;
            }

            try {
                $qm = $this->container->get('app.query_model');
                $id = $qm->getUserId($userName);
                $hash = $this->createHash($newPass, $id);

                if (!$id || !$hash) {
                    $this->addFlash(
                        'error',
                        'Пользователь не существует!'
                    );
                    $errors = true;
                }

                if ($errors){
                    return $this->render('auth/change.html.twig');
                }

                if( $qm->changePasswordById($id, $hash) ) {
                    $this->addFlash(
                        'success',
                        "Вы поменяли пароль для пользователя $userName успешно!"
                    );
                    return $this->redirectToRoute('homepage');
                }
            } catch (\Exception $e){
                $this->addFlash(
                    'error',
                    'По неведомой причине, не удалось сменить пароль, обратитесь к разработчику!'
                );
            }
        }

        return $this->render('auth/change.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }

    /**
     * Create password hash
     *
     * @param $password
     * @param $userId
     * @return null|object
     */
    private function createHash($password, $userId)
    {
        $user = $this->getDoctrine()
                    ->getRepository('AppBundle:User')
                    ->find($userId);

        if (!$user) {
            return $user;
        }

        $encoder = $this->container->get('security.password_encoder');

        return $encoder->encodePassword($user, $password);
    }
}
