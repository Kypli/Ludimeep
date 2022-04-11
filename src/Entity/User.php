<?php

namespace App\Entity;

use App\Repository\UserRepository;

use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    public static $availableRoles = [
        "ROLE_USER" => "ROLE_USER",
        "ROLE_ADMIN" => "ROLE_ADMIN",
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $userName;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $droitImage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $newsletter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="integer", length=20, nullable=true)
     */
    private $adherant;

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
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $roleCa;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateFinMandat;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $membreHonneur;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $commentaire;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getDroitImage(): ?bool
    {
        return $this->droitImage;
    }

    public function setDroitImage(bool $droitImage): self
    {
        $this->droitImage = $droitImage;

        return $this;
    }

    public function getNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): self
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdherant(): ?int
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

    public function getMembreHonneur(): ?bool
    {
        return $this->membreHonneur;
    }

    public function setMembreHonneur(bool $membreHonneur): self
    {
        $this->membreHonneur = $membreHonneur;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }
}
