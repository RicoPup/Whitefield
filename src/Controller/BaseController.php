<?php


namespace App\Controller;


use App\Entity\User;
use App\Service\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BaseController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var Security
     */
    protected $securityService;

    public function __construct(EntityManagerInterface $em, SessionInterface $session, Security $securityService)
    {
        $this->em = $em;
        $this->session = $session;
        $this->securityService = $securityService;
    }

    #check if the user is logged in

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->securityService->isLoggedIn();
    }
}