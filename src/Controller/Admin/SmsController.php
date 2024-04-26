<?php

namespace App\Controller\Admin;

use App\Entity\Sms;
use App\Form\SmsType;
use Instasent\SMSCounter\SMSCounter;
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
        $sms = new Sms();
        $form = $this->createForm(SmsType::class, $sms);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $smsCounter = new SMSCounter();
//            $smsCounter->sanitizeToGSM($form->getData()->content);
            $smsCount = $smsCounter->countWithShiftTables($form->get('content')->getData());
            if ($smsCount->length > $smsCount->per_message) {
                $this->addFlash('danger', 'Erreur lors de l\'envoi de SMS. Veuillez réessayer');
                return $this->redirectToRoute('admin_sms_new');
            }

            if ($form->get('language')->getData() === 'auto') {
                // deepl api
            }

        }
        return $this->render('/admin/sms/new.html.twig', [
            'controller_name' => 'SmsController',
            'newForm' => $form,
        ]);
    }
}
