<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $passwordTempo;

	/**
	 * @ORM\Column(type="json", nullable=true)
	 */
	private $roles = [];

	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $ip;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $anonyme = false;

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

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $accesPhoto = true;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $accesPhotoLanceurAlerte = true;

	/**
	 * @ORM\OneToMany(targetEntity=CommentActu::class, mappedBy="user")
	 */
	private $commentsActu;

	/**
	 * @ORM\OneToMany(targetEntity=CommentPhoto::class, mappedBy="user")
	 */
	private $commentsPhoto;

	/**
	 * @ORM\OneToMany(targetEntity=Actu::class, mappedBy="auteur")
	 */
	private $actus;

	/**
	 * @ORM\OneToMany(targetEntity=Photo::class, mappedBy="user")
	 */
	private $photos;

	/**
	 * @ORM\OneToMany(targetEntity=Photo::class, mappedBy="lanceurAlerte")
	 */
	private $photosLanceurAlerte;

	/**
	 * @ORM\OneToMany(targetEntity=Discussion::class, mappedBy="auteur", orphanRemoval=true)
	 */
	private $discussionsAuteur;

	/**
	 * @ORM\OneToMany(targetEntity=Discussion::class, mappedBy="destinataire", orphanRemoval=true)
	 */
	private $discussionsDestinataire;

	/**
	 * @ORM\OneToMany(targetEntity=Message::class, mappedBy="user", orphanRemoval=true)
	 */
	private $messages;

	public function __construct()
	{
		$this->commentsActu = new ArrayCollection();
		$this->commentsPhoto = new ArrayCollection();
		$this->actus = new ArrayCollection();
		$this->photos = new ArrayCollection();
		$this->photosLanceurAlerte = new ArrayCollection();
		$this->discussionsAuteur = new ArrayCollection();
		$this->discussionsDestinataire = new ArrayCollection();
		$this->messages = new ArrayCollection();
	}

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

	public function getPasswordTempo(): ?string
	{
		return $this->passwordTempo;
	}

	public function setPasswordTempo(string $passwordTempo): self
	{
		$this->passwordTempo = $passwordTempo;

		return $this;
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

	public function getIp(): ?string
	{
		return $this->ip;
	}

	public function setIp(string $ip): self
	{
		$this->ip = $ip;

		return $this;
	}

	public function getAnonyme(): ?bool
	{
		return $this->anonyme;
	}

	public function setAnonyme(bool $anonyme): self
	{
		$this->anonyme = $anonyme;

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

	public function getAccesPhoto(): ?bool
	{
		return $this->accesPhoto;
	}

	public function setAccesPhoto(bool $accesPhoto): self
	{
		$this->accesPhoto = $accesPhoto;

		return $this;
	}

	public function getAccesPhotoLanceurAlerte(): ?bool
	{
		return $this->accesPhotoLanceurAlerte;
	}

	public function setAccesPhotoLanceurAlerte(bool $accesPhotoLanceurAlerte): self
	{
		$this->accesPhotoLanceurAlerte = $accesPhotoLanceurAlerte;

		return $this;
	}

	/**
	 * @return Collection<int, CommentActu>
	 */
	public function getCommentsActu(): Collection
	{
		return $this->commentsActu;
	}

	public function addCommentActu(CommentActu $commentActu): self
	{
		if (!$this->commentsActu->contains($comment)) {
			$this->commentsActu[] = $commentActu;
			$commentActu->setAuteur($this);
		}

		return $this;
	}

	public function removeCommentActu(CommentActu $commentActu): self
	{
		if ($this->commentsActu->removeElement($commentActu)) {
			// set the owning side to null (unless already changed)
			if ($commentActu->getUser() === $this) {
				$commentActu->setUser(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, CommentPhoto>
	 */
	public function getCommentsPhoto(): Collection
	{
		return $this->commentsPhoto;
	}

	public function addCommentPhoto(CommentPhoto $commentPhoto): self
	{
		if (!$this->commentsPhoto->contains($comment)) {
			$this->commentsPhoto[] = $commentPhoto;
			$commentPhoto->setAuteur($this);
		}

		return $this;
	}

	public function removeCommentPhoto(CommentPhoto $commentPhoto): self
	{
		if ($this->commentsPhoto->removeElement($commentPhoto)) {
			// set the owning side to null (unless already changed)
			if ($commentPhoto->getUser() === $this) {
				$commentPhoto->setUser(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Actu>
	 */
	public function getActus(): Collection
	{
		return $this->actus;
	}

	public function addActu(Actu $actu): self
	{
		if (!$this->actus->contains($actu)) {
			$this->actus[] = $actu;
			$actu->setAuteur($this);
		}

		return $this;
	}

	public function removeActu(Actu $actu): self
	{
		if ($this->actus->removeElement($actu)) {
			// set the owning side to null (unless already changed)
			if ($actu->getAuteur() === $this) {
				$actu->setAuteur(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Photo>
	 */
	public function getPhotos(): Collection
	{
		return $this->photos;
	}

	public function addPhoto(Photo $photo): self
	{
		if (!$this->photos->contains($photo)) {
			$this->photos[] = $photo;
			$photo->setUser($this);
		}

		return $this;
	}

	public function removePhoto(Photo $photo): self
	{
		if ($this->photos->removeElement($photo)) {
			// set the owning side to null (unless already changed)
			if ($photo->getUser() === $this) {
				$photo->setUser(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Photo>
	 */
	public function getPhotosLanceurAlerte(): Collection
	{
		return $this->photosLanceurAlerte;
	}

	public function addPhotoLanceurAlerte(Photo $photo): self
	{
		if (!$this->photosLanceurAlerte->contains($photo)) {
			$this->photosLanceurAlerte[] = $photo;
			$photo->setLanceurAlerte($this);
		}

		return $this;
	}

	public function removePhotoLanceurAlerteo(Photo $photo): self
	{
		if ($this->photosLanceurAlerte->removeElement($photo)) {
			// set the owning side to null (unless already changed)
			if ($photo->getLanceurAlerte() === $this) {
				$photo->setLanceurAlerte(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Discussion>
	 */
	public function getDiscussionsAuteur(): Collection
	{
		return $this->discussionsAuteur;
	}

	public function addDiscussionAuteur(Discussion $discussion): self
	{
		if (!$this->discussionsAuteur->contains($discussion)) {
			$this->discussionsAuteur[] = $discussion;
			$discussion->setAuteur($this);
		}

		return $this;
	}

	public function removeDiscussionAuteur(Discussion $discussion): self
	{
		if ($this->discussionsAuteur->removeElement($discussion)) {
			// set the owning side to null (unless already changed)
			if ($discussion->getAuteur() === $this) {
				$discussion->setAuteur(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Discussion>
	 */
	public function getDiscussionsDestinataire(): Collection
	{
		return $this->discussionsDestinataire;
	}

	public function addDiscussionDestinataire(Discussion $discussion): self
	{
		if (!$this->discussionsDestinataire->contains($discussion)) {
			$this->discussionsDestinataire[] = $discussion;
			$discussion->setAuteur($this);
		}

		return $this;
	}

	public function removeDiscussionDestinataire(Discussion $discussion): self
	{
		if ($this->discussionsDestinataire->removeElement($discussion)) {
			// set the owning side to null (unless already changed)
			if ($discussion->getAuteur() === $this) {
				$discussion->setAuteur(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, Message>
	 */
	public function getMessages(): Collection
	{
		return $this->messages;
	}

	public function addMessage(Message $message): self
	{
		if (!$this->messages->contains($message)) {
			$this->messages[] = $message;
			$message->setUser($this);
		}

		return $this;
	}

	public function removeMessage(Message $message): self
	{
		if ($this->messages->removeElement($message)) {
			// set the owning side to null (unless already changed)
			if ($message->getUser() === $this) {
				$message->setUser(null);
			}
		}

		return $this;
	}
}
