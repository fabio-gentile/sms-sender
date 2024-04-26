<?php

namespace App\Controller\Admin;

use App\Entity\Sms;
use App\Form\SmsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SmsController extends AbstractController
{
    #[Route('/admin/sms', name: 'admin_sms')]
    public function index(): Response
    {
        return $this->render('/admin/sms/index.html.twig', [
            'controller_name' => 'SmsController',
        ]);
    }

    #[Route('/admin/sms/envoyer', name: 'admin_sms_new')]
    public function newSms(Request $request): Response
    {
        $user = new Sms();
        $form = $this->createForm(SmsType::class, $user);
        $form->handleRequest($request);
        return $this->render('/admin/sms/new.html.twig', [
            'controller_name' => 'SmsController',
            'newForm' => $form,
        ]);
    }
}
