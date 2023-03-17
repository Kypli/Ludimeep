<?php

namespace App\Entity;

use App\Repository\UserAssoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserAssoRepository::class)
 */
class UserAsso
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $droitImage;

    /**
     * @ORM\Column(type="integer")
     */
    private $adherant = 0;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateInscription;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateFinAdhesion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notoriete;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateFinMandat;

    /**
     * @ORM\Column(type="boolean")
     */
    private $membreHonneur = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Mandat::class, inversedBy="mandataires")
     */
    private $mandat;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="asso", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function isDroitImage(): ?bool
    {
        return $this->droitImage;
    }

    public function setDroitImage(?bool $droitImage): self
    {
        $this->droitImage = $droitImage;

        return $this;
    }

    public function isAdherant(): ?int
    {
        return $this->adherant;
    }

    public function setAdherant(int $adherant): self
    {
        $this->adherant = $adherant;

        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(?\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getDateFinAdhesion(): ?\DateTimeInterface
    {
        return $this->dateFinAdhesion;
    }

    public function setDateFinAdhesion(?\DateTimeInterface $dateFinAdhesion): self
    {
        $this->dateFinAdhesion = $dateFinAdhesion;

        return $this;
    }

    public function getNotoriete(): ?string
    {
        return $this->notoriete;
    }

    public function setNotoriete(?string $notoriete): self
    {
        $this->notoriete = $notoriete;

        return $this;
    }

    public function getRoleCa(): ?string
    {
        return $this->roleCa;
    }

    public function setRoleCa(?string $roleCa): self
    {
        $this->roleCa = $roleCa;

        return $this;
    }

    public function getDateFinMandat(): ?\DateTimeInterface
    {
        return $this->dateFinMandat;
    }

    public function setDateFinMandat(?\DateTimeInterface $dateFinMandat): self
    {
        $this->dateFinMandat = $dateFinMandat;

        return $this;
    }

    public function isMembreHonneur(): ?bool
    {
        return $this->membreHonneur;
    }

    public function setMembreHonneur(bool $membreHonneur): self
    {
        $this->membreHonneur = $membreHonneur;

        return $this;
    }

    public function getMandat(): ?Mandat
    {
        return $this->mandat;
    }

    public function setMandat(?Mandat $mandat): self
    {
        $this->mandat = $mandat;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
