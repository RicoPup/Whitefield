<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends BaseController
{
    /**
     * @return Response
     * @Route("/", name="home")
     */
    public function homepage(): Response
    {
        return $this->render('home/home.html.twig');
    }

    /**
     * @Route("/new-page", name="new-page")
     * @return Response
     */
    public function newpage(): Response
    {
        return $this->render('new_page.html.twig');
    }

}