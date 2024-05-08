<?php

namespace App\Controller\Admin;

use App\Entity\Sms;
use App\Entity\SmsReference;
use App\Entity\SmsTranslation;
use App\Form\SmsType;
use App\Message\SendSms;
use App\Repository\SmsReferenceRepository;
use App\Repository\SmsRepository;
use App\Repository\SmsTranslationRepository;
use App\Repository\UserRepository;
use DeepL\DeepLException;
use Doctrine\ORM\EntityManagerInterface;
use Instasent\SMSCounter\SMSCounter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Attribute\Route;
use DeepL\Translator;

class SmsController extends AbstractController
{
    #[Route('/admin/sms', name: 'admin_sms')]
    public function index(SmsRepository $smsRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $allSms = $smsRepository->findLatests();
        $SMS_PER_PAGE = 10;
        $sms = $paginator->paginate(
            $allSms,
            $request->query->getInt('page', 1),
            $SMS_PER_PAGE
        );
        return $this->render('/admin/sms/index.html.twig', [
            'controller_name' => 'SmsController',
            'allSms' => $sms,
            'totalSms' => count($allSms)
        ]);
    }

    /**
     * @throws DeepLException
     */
    #[Route('/admin/sms/envoyer', name: 'admin_sms_new')]
    public function newSms(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, MessageBusInterface $bus): Response
    {
        $form = $this->createForm(SmsType::class, new Sms());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //  Vérification of the sms
            $smsCounter = new SMSCounter();
            $smsCount = $smsCounter->countWithShiftTables($form->get('content')->getData());

            // content > 160 characters
            if ($smsCount->length > $smsCount->per_message) {
                $this->addFlash('danger', 'Erreur lors de l\'envoi de SMS. Veuillez réessayer');
                return $this->redirectToRoute('admin_sms_new');
            }

            $content = $smsCounter->sanitizeToGSM($form->get('content')->getData());
            $language = $form->get('language')->getData();
            $scheduledAt = $form->get('scheduledAt')->getData();

            $sms = new Sms();
            $sms->setContent($content)
                ->setLanguage($language)
                ->setScheduledAt($scheduledAt);
            $entityManager->persist($sms);

            $users = [];
            if ($language === 'auto') {
                $translator = new Translator($this->getParameter('deepl_api'));
                $languages = $userRepository->findDistinctLanguages();

                // Get all the translations
                foreach ($languages as $language) {
                    $translation = $translator->translateText($content, null, $language['language']);
                    $smsTranslation = new SmsTranslation();
                    $smsTranslation->setSms($sms)
                        ->setLanguage($language['language'])
                        ->setContent($translation->text);

                    $entityManager->persist($smsTranslation);
                }

                $users = $userRepository->findAll();
            } else {
                // Get only the users with the same language
                $users = $userRepository->findByLanguage($language);
            }

            // Creating sms references
            foreach ($users as $user) {
                $smsReference = new SmsReference();
                $smsReference->setSms($sms)
                    ->setStatus('PENDING')
                    ->setUser($user);
                $entityManager->persist($smsReference);
            }

            $entityManager->flush();
            $bus->dispatch(new SendSms($sms->getId()), [new DelayStamp(90000)]);
            $this->addFlash('success', 'Message ajouté. L\'envoi a été programmé.');
            return $this->redirectToRoute('admin_dashboard');
        }
        return $this->render('/admin/sms/new.html.twig', [
            'controller_name' => 'SmsController',
            'newForm' => $form,
        ]);
    }

    #[Route('/admin/sms/{id}', name: 'admin_sms_show', requirements: ['id' => '\d+'])]
    public function show(Request $request, EntityManagerInterface $entityManager, Sms $sms, UserRepository $userRepository, SmsReferenceRepository $smsReferenceRepository): Response
    {
        $totalUsers = $userRepository->findCountUsers();
        $users = $smsReferenceRepository->findAllSentSms($sms);
        return $this->render('/admin/sms/show.html.twig', [
            'sms' => $sms,
            'totalUsers' => $totalUsers,
            'users' => count($users)
        ]);
    }

    #[Route('/admin/sms/{id}/utilisateurs', name: 'admin_sms_show_users', requirements: ['id' => '\d+'])]
    public function showUsers(Request $request, EntityManagerInterface $entityManager, Sms $sms): Response
    {

        return $this->render('/admin/sms/show.html.twig', [
            'sms' => $sms
        ]);
    }

    #[Route('/admin/sms/{id}/edit', name: 'admin_sms_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Sms $sms): Response
    {

        return $this->render('/admin/sms/show.html.twig', [
            'sms' => $sms
        ]);
    }
}
