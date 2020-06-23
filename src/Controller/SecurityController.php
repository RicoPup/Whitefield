<?php


namespace App\Controller;


use App\Entity\User;
use App\Service\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends BaseController
{
    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function login(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        if ($request->isMethod("POST")) {
            $user = $this->em->getRepository(User::class)->findOneBy(['username' => $request->request->get('username')]);
            if ($user) {
                $plainPassword = $request->request->get('password');
                $match = $encoder->isPasswordValid($user, $plainPassword);
                if ($match) {
                    $this->session->set('user', $user);
                    return $this->redirectToRoute('my_account');
                }
            }
        }
        return $this->render('home/login.html.twig');
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        if ($request->isMethod("POST")) {
            $user = $this->em->getRepository(User::class)->findOneBy(['username' => $request->request->get('username')]);
            if (!($user)) {
                $user = new User();
                $user->setUsername($request->request->get('username'));
                $user->setEmail($request->request->get('email'));

                #password stuff
                $plainPassword = $request->request->get('password');
                $encoded = $encoder->encodePassword($user, $plainPassword);
                $user->setPassword($encoded);

                $this->em->persist($user);
                $this->em->flush();
            }
        }
        return $this->render('home/register.html.twig');
    }

    /**
     * @Route("/change-password", name="change-password")
     * @param Security $security
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param SessionInterface $session
     * @return Response
     */
    public function changePassword(Security $security, Request $request, UserPasswordEncoderInterface $encoder, SessionInterface $session): Response
    {
        if ($this->isLoggedIn()) {
            if ($request->isMethod("POST")) {
                $user = $security->getUser($session);

                //get plain passwords
                $newPlainPassword = $request->request->get('new-password');
                $passwordMatch = $request->request->get('repeat-password');
                $plainPassword = $request->request->get('password');

                //encode them
                $encodedNewPass = $encoder->encodePassword($user, $newPlainPassword);
                $encodedMatch = $encoder->encodePassword($user, $passwordMatch);
                $encodedPass = $encoder->encodePassword($user, $plainPassword);

                //check if the password is valid (duh)
                $correctPass = $encoder->isPasswordValid($user, $plainPassword);

                //check if the new passwords they typed are the same
                $newPasswordsMatch = false;
                if ($newPlainPassword == $passwordMatch) {
                    $newPasswordsMatch = true;
                }

                $userPass = $user->getPassword();
                if ($newPasswordsMatch) {
                    if ($correctPass) {
                        $user->setPassword($encodedNewPass);

                        $this->em->persist($user);
                        $this->em->flush();

                        //if I don't set this, the session will get logged out when the passwords are checked with 'isLoggedIn()'
                        $session->get('user')->setPassword($encodedNewPass);

                        $this->addFlash("MESSAGE", "Your password was changed successfully!");

                        return $this->redirectToRoute('my_account');
                    } else {
                        $this->addFlash("MESSAGE", "Your password was incorrect");

                        return $this->redirectToRoute('change-password');
                    }
                } else {
                    $this->addFlash("MESSAGE", "Your new passwords do not match");

                    return $this->redirectToRoute('change-password');
                }

            }
        }
        return $this->render('home/change_password.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     * @return RedirectResponse
     */
    public function logout()
    {
        $this->session->remove('user');
        return $this->redirectToRoute('login');
    }
}