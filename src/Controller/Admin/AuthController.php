<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    #[Route('/admin/login', name: 'admin_login')]
    public function index(): Response
    {
        return $this->render('admin/auth/index.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }
}
