<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_users')]
    public function index(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $allUsers = $userRepository->findLatestsRegistered();
        $USER_PER_PAGE = 10;
        $users = $paginator->paginate(
            $allUsers,
            $request->query->getInt('page', 1),
            $USER_PER_PAGE
        );
        return $this->render('admin/user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
            'totalUsers' => count($allUsers)
        ]);
    }
}
