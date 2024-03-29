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
     * @ORM\Column(type="boolean")
     */
    private $newsletter = false;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $commentaire;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = 1;

    /**
     * @ORM\OneToOne(targetEntity=UserProfil::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $profil;

    /**
     * @ORM\OneToOne(targetEntity=UserAsso::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $asso;

    /**
     * @ORM\OneToMany(targetEntity=Actu::class, mappedBy="auteur")
     */
    private $actus;

    /**
     * @ORM\OneToMany(targetEntity=CommentActu::class, mappedBy="user")
     */
    private $commentsActu;

    /**
     * @ORM\OneToMany(targetEntity=Photo::class, mappedBy="user")
     */
    private $photos;

    /**
     * @ORM\OneToMany(targetEntity=CommentPhoto::class, mappedBy="user")
     */
    private $commentsPhoto;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accesPhoto = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accesPhotoLanceurAlerte = true;

    /**
     * @ORM\OneToMany(targetEntity=Photo::class, mappedBy="lanceurAlerte")
     */
    private $photosLanceurAlerte;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="user", orphanRemoval=true)
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity=Discussion::class, mappedBy="auteur", orphanRemoval=true)
     */
    private $discussionsAuteur;

    /**
     * @ORM\OneToMany(targetEntity=Discussion::class, mappedBy="destinataire", orphanRemoval=true)
     */
    private $discussionsDestinataire;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="owner")
     */
    private $games;

    /**
     * @ORM\OneToMany(targetEntity=SondageUser::class, mappedBy="votant")
     */
    private $sondages;

    /**
     * @ORM\ManyToMany(targetEntity=Seance::class, mappedBy="presents")
     */
    private $seances;

    /**
     * @ORM\OneToMany(targetEntity=Tchat::class, mappedBy="user")
     */
    private $tchats;

    /**
     * @ORM\OneToMany(targetEntity=Table::class, mappedBy="gerant", orphanRemoval=true)
     */
    private $gerantTables;

    /**
     * @ORM\ManyToMany(targetEntity=Table::class, mappedBy="players")
     */
    private $tables;

    /**
     * @ORM\OneToMany(targetEntity=Operation::class, mappedBy="user", orphanRemoval=true)
     */
    private $operations;

    /**
     * @ORM\OneToMany(targetEntity=Organigramme::class, mappedBy="user", orphanRemoval=true)
     */
    private $organigrammes;

    public function __construct()
    {
        $this->actus = new ArrayCollection();
        $this->commentsActu = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->commentsPhoto = new ArrayCollection();
        $this->photosLanceurAlerte = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->discussionsAuteur = new ArrayCollection();
        $this->discussionsDestinataire = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->sondages = new ArrayCollection();
        $this->seances = new ArrayCollection();
        $this->tchats = new ArrayCollection();
        $this->gerantTables = new ArrayCollection();
        $this->tables = new ArrayCollection();
        $this->operations = new ArrayCollection();
        $this->organigrammes = new ArrayCollection();
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

    public function isAdmin(): ?bool
    {
        return in_array('ROLE_ADMIN', $this->roles)
            ? true
            : false
        ;
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

    public function isNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(?bool $newsletter): self
    {
        $this->newsletter = $newsletter;

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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getProfil(): ?UserProfil
    {
        return $this->profil;
    }

    public function setProfil(UserProfil $profil): self
    {
        // set the owning side of the relation if necessary
        if ($profil->getUser() !== $this) {
            $profil->setUser($this);
        }

        $this->profil = $profil;

        return $this;
    }

    public function getAsso(): ?UserAsso
    {
        return $this->asso;
    }

    public function setAsso(UserAsso $asso): self
    {
        // set the owning side of the relation if necessary
        if ($asso->getUser() !== $this) {
            $asso->setUser($this);
        }

        $this->asso = $asso;

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
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setOwner($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getOwner() === $this) {
                $game->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sondage>
     */
    public function getSondages(): Collection
    {
        return $this->sondages;
    }

    public function addSondage(Sondage $sondage): self
    {
        if (!$this->sondages->contains($sondage)) {
            $this->sondages[] = $sondage;
            $sondage->addVotant($this);
        }

        return $this;
    }

    public function removeSondage(Sondage $sondage): self
    {
        if ($this->sondages->removeElement($sondage)) {
            $sondage->removeVotant($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Seance>
     */
    public function getSeances(): Collection
    {
        return $this->seances;
    }

    public function addSeance(Seance $seance): self
    {
        if (!$this->seances->contains($seance)) {
            $this->seances[] = $seance;
            $seance->addPresent($this);
        }

        return $this;
    }

    public function removeSeance(Seance $seance): self
    {
        if ($this->seances->removeElement($seance)) {
            $seance->removePresent($this);
        }

        return $this;
    }

    public function inSeance(Seance $seance): bool
    {
        if ($this->seances->contains($seance)) {
            return true;
        }

        return false;
    }

    /**
     * @return Collection<int, Tchat>
     */
    public function getTchats(): Collection
    {
        return $this->tchats;
    }

    public function addTchat(Tchat $tchat): self
    {
        if (!$this->tchats->contains($tchat)) {
            $this->tchats[] = $tchat;
            $tchat->setUser($this);
        }

        return $this;
    }

    public function removeTchat(Tchat $tchat): self
    {
        if ($this->tchats->removeElement($tchat)) {
            // set the owning side to null (unless already changed)
            if ($tchat->getUser() === $this) {
                $tchat->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Table>
     */
    public function getGerantTables(): Collection
    {
        return $this->gerantTables;
    }

    public function addGerantTable(Table $gerantTable): self
    {
        if (!$this->gerantTables->contains($gerantTable)) {
            $this->gerantTables[] = $gerantTable;
            $gerantTable->setGerant($this);
        }

        return $this;
    }

    public function removeGerantTable(Table $gerantTable): self
    {
        if ($this->gerantTables->removeElement($gerantTable)) {
            // set the owning side to null (unless already changed)
            if ($gerantTable->getGerant() === $this) {
                $gerantTable->setGerant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Table>
     */
    public function getTables(): Collection
    {
        return $this->tables;
    }

    public function addTable(Table $table): self
    {
        if (!$this->tables->contains($table)) {
            $this->tables[] = $table;
            $table->addUser($this);
        }

        return $this;
    }

    public function removeTable(Table $table): self
    {
        if ($this->tables->removeElement($table)) {
            $table->removeUser($this);
        }

        return $this;
    }


    public function isInscrit(User $user, Table $table): Bool
    {
        if ($table->getPlayers()->contains($user)){
            return true;
        }

        return false;
    }

    /**
     * @return Collection<int, Operation>
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): self
    {
        if (!$this->operations->contains($operation)) {
            $this->operations[] = $operation;
            $operation->setUser($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->removeElement($operation)) {
            // set the owning side to null (unless already changed)
            if ($operation->getUser() === $this) {
                $operation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Organigramme>
     */
    public function getOrganigrammes(): Collection
    {
        return $this->organigrammes;
    }

    public function addOrganigramme(Organigramme $organigramme): self
    {
        if (!$this->organigrammes->contains($organigramme)) {
            $this->organigrammes[] = $organigramme;
            $organigramme->setUser($this);
        }

        return $this;
    }

    public function removeOrganigramme(Organigramme $organigramme): self
    {
        if ($this->organigrammes->removeElement($organigramme)) {
            // set the owning side to null (unless already changed)
            if ($organigramme->getUser() === $this) {
                $organigramme->setUser(null);
            }
        }

        return $this;
    }
}
