<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use libphonenumber\PhoneNumberUtil;

class AuthController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $utils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_dashboard');
        }

        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        $loginError = null;

        if ($error instanceof TooManyLoginAttemptsAuthenticationException) {
            $loginError = 'Trop de tentatives de connexion. Réssayez plus tard';
        }

        return $this->render('auth/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username,
            'loginError' => $loginError,
        ]);
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,  EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, PhoneNumberUtil $phoneNumberUtil): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_dashboard');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $countryIso = $phoneNumberUtil->getRegionCodeForCountryCode($form->get('phoneNumber')->getData()->getCountryCode());
            $user->setCountry($countryIso);
//            $user->setRoles(['ROLE_ADMIN']);
            $entityManager->persist($user);
            $entityManager->flush();

            // login the user automatically after the registration
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $tokenStorage->setToken($token);

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(AuthenticationUtils $utils): Response
    {

    }
}
