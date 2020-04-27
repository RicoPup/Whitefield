<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
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
     * @return Response
     * @Route("/new-page", name="new-page")
     */
    public function newpage(): Response
    {
        return $this->render('new_page.html.twig');
    }


}