<?php

namespace App\Service;

use App\Entity\Sms;
use App\Entity\SmsReference;
use App\Repository\SmsReferenceRepository;
use App\Repository\SmsRepository;
use App\Repository\SmsTranslationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;

class SendSmsService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly SmsRepository $smsRepository,
        private readonly SmsReferenceRepository $smsReferenceRepository,
        private readonly SmsTranslationRepository $smsTranslationRepository,
        private readonly EntityManagerInterface $entityManager,
        private TexterInterface $texter
    ) {}

    /**
     * Start the proccess.
     * @return void
     */
    public function startProcessing(): void
    {
        // Retrieve all unsent SMS
        $unsentSmsList = $this->smsRepository->findUnsetSms();

        // Process each unsent SMS
        foreach ($unsentSmsList as $unsentSms) {
            /* @var $unsentSms Sms */
            if ($unsentSms->getScheduledAt()->getTimestamp() <= (new \DateTime())->getTimestamp()) {
                $unsentSms->setStatus('PROCESSING');
                $this->entityManager->persist($unsentSms);
                $this->entityManager->flush();
                $this->processUnsentSms($unsentSms);
            }
        }
    }

    /**
     * Process unsent SMS.
     * @param Sms $unsentSms
     * @return void
     */
    private function processUnsentSms(Sms $unsentSms): void
    {
        // Retrieve language and SMS references
        $language = $unsentSms->getLanguage();
        $smsReferences = $this->smsReferenceRepository->findAllBySms($unsentSms);

        foreach ($smsReferences as $smsReference) {
            if ($smsReference->getStatus() === 'PENDING') {
                $this->sendSms($smsReference, $unsentSms, $language);
            }
            $this->entityManager->persist($smsReference);
        }

        $this->entityManager->flush();

        // Check if all SMS references have been sent
        if ($this->allSmsReferencesSent($unsentSms)) {
            $this->handleSentSms($unsentSms, $language);
        } else {
            $unsentSms->setStatus('RETRY');
            $this->entityManager->persist($unsentSms);
            $this->entityManager->flush();
        }
    }

    /**
     * Send an SMS.
     * @param SmsReference $smsReference
     * @param Sms $unsentSms
     * @param string $language
     * @return void
     */
    private function sendSms(SmsReference $smsReference, Sms $unsentSms, string $language): void
    {
        $smsReference->setStatus('SENDING');
        $this->entityManager->persist($smsReference);

        // Retrieve user phone number and language
        $userPhoneNumber = $this->userRepository->findPhoneNumberById($smsReference->getUser());
        $userLanguage = $this->userRepository->findLanguageById($smsReference->getUser());

        // Retrieve SMS content
        $content = $language === 'auto'
            ? $this->smsTranslationRepository->findContentByLanguage($unsentSms, $userLanguage)
            : $unsentSms->getContent();

//        Uncomment if you want to use Twilio api
//        $twilioSms = new SmsMessage($userPhoneNumber, $content);
//        $isSent = $this->texter->send($twilioSms);
//        if ($isSent) {
//            $smsReference->setStatus('SENT');
//        }

        // Comment if you use Twilio Api
        $smsReference->setStatus('SENT');
    }

    /**
     * Checks if all SMS references have been sent.
     * @param Sms $unsentSms
     * @return bool
     */
    private function allSmsReferencesSent(Sms $unsentSms): bool
    {
        $smsReferencesSent = $this->smsReferenceRepository->findAllSentSms($unsentSms);
        $smsReferencesCount = $this->smsReferenceRepository->findCountSms($unsentSms);
        return $smsReferencesCount === count($smsReferencesSent);
    }

    /**
     * Handles actions after all SMS have been sent.
     * @param Sms $unsentSms
     * @param string $language
     * @return void
     */
    private function handleSentSms(Sms $unsentSms, string $language): void
    {
        // If language is auto, delete translations
        if ($language === 'auto') {
            $this->smsTranslationRepository->deleteContentSms($unsentSms);
        }

        // Set sent date and flush entity manager
        $unsentSms->setSentAt(new \DateTime())
            ->setStatus('SENT');
        $this->entityManager->persist($unsentSms);
        $this->entityManager->flush();
    }
}
