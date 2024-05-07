<?php

namespace App\Entity;

use App\Repository\SmsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SmsRepository::class)]
class Sms
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ['auto', 'fr', 'nl', 'en-US', 'es', 'it'])]
    private ?string $language = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $modifiedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sentAt = null;

    /**
     * @var Collection<int, SmsReference>
     */
    #[ORM\OneToMany(targetEntity: SmsReference::class, mappedBy: 'Sms')]
    private Collection $smsReferences;

    /**
     * @var Collection<int, SmsTranslation>
     */
    #[ORM\OneToMany(targetEntity: SmsTranslation::class, mappedBy: 'sms', orphanRemoval: true)]
    private Collection $smsTranslations;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $scheduledAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->smsReferences = new ArrayCollection();
        $this->smsTranslations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTimeInterface $modifiedAt): static
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * @return Collection<int, SmsReference>
     */
    public function getSmsReferences(): Collection
    {
        return $this->smsReferences;
    }

    public function addSmsReference(SmsReference $smsReference): static
    {
        if (!$this->smsReferences->contains($smsReference)) {
            $this->smsReferences->add($smsReference);
            $smsReference->setSms($this);
        }

        return $this;
    }

    public function removeSmsReference(SmsReference $smsReference): static
    {
        if ($this->smsReferences->removeElement($smsReference)) {
            // set the owning side to null (unless already changed)
            if ($smsReference->getSms() === $this) {
                $smsReference->setSms(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SmsTranslation>
     */
    public function getSmsTranslations(): Collection
    {
        return $this->smsTranslations;
    }

    public function addSmsTranslation(SmsTranslation $smsTranslation): static
    {
        if (!$this->smsTranslations->contains($smsTranslation)) {
            $this->smsTranslations->add($smsTranslation);
            $smsTranslation->setSms($this);
        }

        return $this;
    }

    public function removeSmsTranslation(SmsTranslation $smsTranslation): static
    {
        if ($this->smsTranslations->removeElement($smsTranslation)) {
            // set the owning side to null (unless already changed)
            if ($smsTranslation->getSms() === $this) {
                $smsTranslation->setSms(null);
            }
        }

        return $this;
    }

    public function getScheduledAt(): ?\DateTimeInterface
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(\DateTimeInterface $scheduledAt): static
    {
        $this->scheduledAt = $scheduledAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
