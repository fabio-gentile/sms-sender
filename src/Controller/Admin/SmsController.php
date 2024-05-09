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
        $sms = new Sms();
        $sms->setLanguage('auto')
            ->setScheduledAt(new \DateTime());

        $form = $this->createForm(SmsType::class, $sms);
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

            $sms->setContent($content)
                ->setLanguage($language)
                ->setScheduledAt($scheduledAt);
            $entityManager->persist($sms);

            $this->createTranslationsAndReferences($language, $userRepository, $content, $sms, $entityManager, $bus);
            $this->addFlash('success', 'Message ajouté. L\'envoi a été programmé.');
            return $this->redirectToRoute('admin_sms');
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
        $users = $smsReferenceRepository->findAllUsersBySms($sms);
        return $this->render('/admin/sms/show.html.twig', [
            'sms' => $sms,
            'totalUsers' => $totalUsers,
            'users' => count($users)
        ]);
    }

    #[Route('/admin/sms/{id}/utilisateurs', name: 'admin_sms_show_users', requirements: ['id' => '\d+'])]
    public function showUsers(Request $request, EntityManagerInterface $entityManager, Sms $sms, PaginatorInterface $paginator,SmsReferenceRepository $smsReferenceRepository, int $id, SmsRepository $smsRepository): Response
    {
        $allUsers = $smsReferenceRepository->findAllUsersBySms($sms);
        $USER_PER_PAGE = 10;
        $users = $paginator->paginate(
            $allUsers,
            $request->query->getInt('page', 1),
            $USER_PER_PAGE
        );
        return $this->render('admin/sms/users.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
            'totalUsers' => count($allUsers),
            'sms' => $sms
        ]);
    }

    #[Route('/admin/sms/{id}/edit', name: 'admin_sms_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Sms $sms, UserRepository $userRepository, SmsReferenceRepository $smsReferenceRepository, SmsTranslationRepository $smsTranslationRepository, MessageBusInterface $bus): Response
    {
        if ($sms->getStatus() || $sms->getSentAt()) {
            return $this->redirectToRoute('admin_sms');
        }

        $originalSms = clone $sms;

        $form = $this->createForm(SmsType::class, $sms);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$this->smsHasChanged($originalSms, $sms)) {
                $this->addFlash('info', 'Aucun changement détecté, le sms n\'a pas été modifié.');
                return $this->redirectToRoute('admin_sms');
            }

            //  Vérification of the sms
            $smsCounter = new SMSCounter();
            $smsCount = $smsCounter->countWithShiftTables($form->get('content')->getData());

            // content > 160 characters
            if ($smsCount->length > $smsCount->per_message) {
                $this->addFlash('danger', 'Erreur lors de la modification du SMS.');
                return $this->redirectToRoute('admin_sms');
            }

            $content = $smsCounter->sanitizeToGSM($form->get('content')->getData());
            $language = $form->get('language')->getData();
            $scheduledAt = $form->get('scheduledAt')->getData();

            $sms->setContent($content)
                ->setLanguage($language)
                ->setScheduledAt($scheduledAt)
                ->setModifiedAt(new \DateTime());
            $entityManager->persist($sms);

            // delete all existing references and translations
            $smsTranslationRepository->deleteContentSms($sms);
            $smsReferenceRepository->deleteBySms($sms);

            $this->createTranslationsAndReferences($language, $userRepository, $content, $sms, $entityManager, $bus);
            $this->addFlash('success', 'Le message a été modifié.');
            return $this->redirectToRoute('admin_sms');
        }

        return $this->render('/admin/sms/edit.html.twig', [
            'sms' => $sms,
            'myForm' => $form->createView()
        ]);
    }

    #[Route('/admin/sms/{id}/cancel', name: 'admin_sms_cancel', requirements: ['id' => '\d+'])]
    public function cancel(EntityManagerInterface $entityManager, Sms $sms, SmsReferenceRepository $smsReferenceRepository, SmsTranslationRepository $smsTranslationRepository): Response
    {
        if ($sms->getStatus() === 'SENT' || $sms->getSentAt()) {
            return $this->redirectToRoute('admin_sms');
        }

        $smsTranslationRepository->deleteContentSms($sms);
        $smsReferences = $smsReferenceRepository->findAllBySms($sms);
        foreach ($smsReferences as $smsReference) {
            /* @var $smsReference SmsReference */
            $smsReference->setStatus('CANCELLED');
            $entityManager->persist($smsReference);
        }

        $sms->setStatus('CANCELLED')
            ->setModifiedAt(new \DateTime())
            ->setScheduledAt(new \DateTime())
            ;

        $entityManager->persist($sms);
        $entityManager->flush();

        $this->addFlash('info', 'Le message a été annulé.');
        return $this->redirectToRoute('admin_sms');
    }

    /**
     * Verifiy if the sms has been modified.
     * @param Sms $originalSms
     * @param Sms $modifiedSms
     * @return bool
     */
    private function smsHasChanged(Sms $originalSms, Sms $modifiedSms): bool
    {
        if ($originalSms->getContent() !== $modifiedSms->getContent() ||
            $originalSms->getLanguage() !== $modifiedSms->getLanguage() ||
            $originalSms->getScheduledAt() !== $modifiedSms->getScheduledAt()) {
            return true;
        }

        return false;
    }

    /**
     * Create sms translations and sms references for the specific sms.
     * @param mixed $language
     * @param UserRepository $userRepository
     * @param bool|string $content
     * @param Sms $sms
     * @param EntityManagerInterface $entityManager
     * @param MessageBusInterface $bus
     * @return void
     * @throws DeepLException
     */
    private function createTranslationsAndReferences(mixed $language, UserRepository $userRepository, bool|string $content, Sms $sms, EntityManagerInterface $entityManager, MessageBusInterface $bus): void
    {
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
    }
}
