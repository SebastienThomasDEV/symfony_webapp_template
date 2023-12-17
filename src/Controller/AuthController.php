<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class AuthController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, Security $security): Response
    {
        $message = '';
            $response = $this->checkForm($request->request->all(), $em, $request);
        if ($request->isMethod('POST')) {
            if ($response['isValid']) {
                $user = new User();
                $user->setEmail($request->request->get('_email'));
                $user->setPassword($passwordHasher->hashPassword($user, $request->request->get('_password')));
                $user->setRoles(['ROLE_USER']);
                $em->persist($user);
                $em->flush();
                $security->login($user);
                $this->addFlash('success', 'Votre compte a bien été créé');
                return $this->redirectToRoute('app_dashboard');
            }
        }
        return $this->render('auth/register.html.twig', [
            'controller_name' => 'AuthController',
            'response' => $response
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(Request $request, Security $security): Response
    {
        return $this->render('auth/login.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Security $security): Response
    {
        $security->logout(true);
        return $this->redirectToRoute('app_home');
    }

    public function checkForm(array $attrs, EntityManagerInterface $em, Request $request): array
    {
        $response = [
            'isValid' => true,
            'isSubmitted' => $request->isMethod('POST'),
            'email' => "",
            'password' => "",
        ];
        if ($response['isSubmitted'] === false) {
            return $response;
        }
        if (strlen($attrs['_password']) < 5) {
            $response['password'] = "The password must be at least 5 characters long";
            $response['isValid'] = false;
        }
        if (strlen($attrs['_password']) > 50) {
            $response['password'] = "The password must be less than 50 characters long";
            $response['isValid'] = false;
        }
        if (preg_match('/[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+/', $attrs['_email']) === 0) {
            $response['email'] = "The email is not valid";
            $response['isValid'] = false;
        }
        if ($em->getRepository(User::class)->findOneBy(['email' => $attrs['_email']])) {
            $response['email'] = "This email is already used";
            $response['isValid'] = false;
        }
        if ($attrs['_password'] !== $attrs['_password_confirm']) {
            $response['password'] = "The passwords does not match";
            $response['isValid'] = false;
        }

        if ($attrs['_email'] === '') {
            $response['email'] = "You must fill the email field";
            $response['isValid'] = false;
        }
        if ($attrs['_password'] === '') {
            $response['password'] = "You must fill the password field";
            $response['isValid'] = false;
        }
        return $response;
    }
}
