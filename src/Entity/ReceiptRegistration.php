<?php

namespace App\Entity;

use App\Repository\ReceiptRegistrationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReceiptRegistrationRepository::class)
 */
class ReceiptRegistration
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $store;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $receiptCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $receiptDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $acordTermeni;

    /**
     * @ORM\Column(type="boolean")
     */
    private $acordVarsta;

    /**
     * @ORM\Column(type="boolean")
     */
    private $acordRegulament;

    /**
     * @ORM\Column(type="float", unique=true)
     */
    private $idNet;

    /**
     * @ORM\Column(type="datetime")
     */
    private $submittedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getStore(): ?string
    {
        return $this->store;
    }

    public function setStore(string $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getReceiptCode(): ?string
    {
        return $this->receiptCode;
    }

    public function setReceiptCode(string $receiptCode): self
    {
        $this->receiptCode = $receiptCode;

        return $this;
    }

    public function getReceiptDate(): ?string
    {
        return $this->receiptDate;
    }

    public function setReceiptDate(string $receiptDate): self
    {
        $this->receiptDate = $receiptDate;

        return $this;
    }

    public function isAcordTermeni(): ?bool
    {
        return $this->acordTermeni;
    }

    public function setAcordTermeni(bool $acordTermeni): self
    {
        $this->acordTermeni = $acordTermeni;

        return $this;
    }

    public function isAcordVarsta(): ?bool
    {
        return $this->acordVarsta;
    }

    public function setAcordVarsta(bool $acordVarsta): self
    {
        $this->acordVarsta = $acordVarsta;

        return $this;
    }

    public function isAcordRegulament(): ?bool
    {
        return $this->acordRegulament;
    }

    public function setAcordRegulament(bool $acordRegulament): self
    {
        $this->acordRegulament = $acordRegulament;

        return $this;
    }

    public function getIdNet(): ?int
    {
        return $this->idNet;
    }

    public function setIdNet(int $idNet): self
    {
        $this->idNet = $idNet;

        return $this;
    }

    public function getSubmittedAt(): ?\DateTimeInterface
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(\DateTimeInterface $submittedAt): self
    {
        $this->submittedAt = $submittedAt;

        return $this;
    }
}
