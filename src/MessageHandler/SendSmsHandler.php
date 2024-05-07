<?php

namespace App\MessageHandler;

use App\Message\SendSms;
use App\Service\SendSmsService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


#[AsMessageHandler]
final class SendSmsHandler
{
    public function __construct(private readonly SendSmsService $sendSmsService) {}

    public function __invoke(SendSms $message): void
    {
        $this->sendSmsService->startProcessing();
    }
}
