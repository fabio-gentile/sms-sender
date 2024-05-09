<?php

namespace App\Controller\Admin;

use App\Repository\SmsRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(SmsRepository $smsRepository, UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $PER_PAGE = 10;

        $allUsers = $userRepository->findLatestsRegistered();
        $users = $paginator->paginate(
            $allUsers,
            $request->query->getInt('users', 1),
            $PER_PAGE
        );

        $allSms = $smsRepository->findLatests();
        $sms = $paginator->paginate(
            $allSms,
            $request->query->getInt('sms', 1),
            $PER_PAGE
        );
        return $this->render('admin/dashboard/index.html.twig', [

            'totalUsers' => count($allUsers),
            'totalSms' => count($allSms),
            'users' => $users,
            'allSms' => $sms
        ]);
    }
}
