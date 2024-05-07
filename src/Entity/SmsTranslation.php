<?php

namespace App\Entity;

use App\Repository\SmsTranslationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SmsTranslationRepository::class)]
class SmsTranslation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $language = null;

    #[ORM\ManyToOne(inversedBy: 'smsTranslations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sms $sms = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function getSms(): ?Sms
    {
        return $this->sms;
    }

    public function setSms(?Sms $sms): static
    {
        $this->sms = $sms;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }
}
