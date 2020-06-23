<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Security
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var SessionInterface
     */
    protected $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    // check if the user is logged in
    public function isLoggedIn(): bool
    {
        /** @var User $user */
        $user = $this->session->get('user');

        // check if there is a session
        if($user) {

            // find the user in the DB that matches the one in the session
            $dbUser = $this->em->getRepository(User::class)->findOneBy([
                'username' => $user->getUsername(),
                'password' => $user->getPassword(),
            ]);

            // if we found one, fucking woopie
            if ($dbUser) {
                return true;
            }
        }
        return false;
    }

    public function getUser(SessionInterface $session)
    {
        /** @var User $sessionUser */
        $sessionUser = $session->get('user');
        $sessionUsername = $sessionUser->getUsername();
        $sessionPassword = $sessionUser->getPassword();

        $dbUser = $this->em->getRepository(User::class)->findOneBy(['username' => $sessionUsername]);
        $dbUserPassword = $dbUser->getPassword();

        if ($sessionPassword !== $dbUserPassword) {
            $this->session->remove('user');
            return null;
        }
        return $dbUser;
    }

    public function getUserForTwig()
    {
        $username = $this->session->get('user')->getUsername();

        return $username;
    }
}