<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyAccountController extends BaseController
{
    /**
     * @Route("/my_account", name="my_account")
     * @return Response
     */
    public function MyAccount(): Response
    {
        if(!$this->isLoggedIn()) {
            return $this->redirectToRoute('login');
        }
        return $this->render('account/my_account.html.twig');
    }
}