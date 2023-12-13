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
        if ($request->isMethod('POST')) {
            $message = $this->checkForm($request->request->all(), $em, $request);
            if ($message === true) {
                $user = new User();
                $user->setEmail($request->request->get('_email'));
                $user->setPassword($passwordHasher->hashPassword($user, $request->request->get('_password')));
                $user->setRoles(['ROLE_USER']);
                $em->persist($user);
                $em->flush();
                $security->login($user);
                $this->addFlash('success', 'Votre compte a bien été créé');
                return $this->redirectToRoute('app_dashboard');
            } else {
                $this->addFlash('error', $message);
            }
        }
        return $this->render('auth/register.html.twig', [
            'controller_name' => 'AuthController',
            'message' => $message === true ? '' : $message
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(): Response
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

    public function checkForm(array $attrs, EntityManagerInterface $em, Request $request): bool|string
    {
        $message = '';
        if (is_null($attrs['_email']) || is_null($attrs['_password'])) {
            $message = "Veuillez remplir tous les champs";
        }
        if ($attrs['_password'] !== $attrs['password_confirm']) {
            $message = "Les mots de passe ne correspondent pas";
        }
        if ($em->getRepository(User::class)->findOneBy(['email' => $request->request->get('_email')])) {
            $message = "Création de compte impossible";
        }
        if (preg_match('/[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+/', $attrs['_email']) === 0) {
            $message = "Veuillez entrer une adresse email valide";
        }
        if (strlen($attrs['_password']) < 8) {
            $message = "Le mot de passe doit contenir au moins 8 caractères";
        }
        if (strlen($attrs['_password']) > 255) {
            $message = "Le mot de passe doit contenir au maximum 255 caractères";
        }
        if (strlen($attrs['_email']) > 255) {
            $message = "L'adresse email doit contenir au maximum 255 caractères";
        }
        return $message === '' ? true : $message;
    }
}
