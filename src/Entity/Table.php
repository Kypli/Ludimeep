<?php

namespace App\Entity;

use App\Repository\TableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TableRepository::class)
 * @ORM\Table(name="`table`")
 */
class Table
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="tables")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gameFree;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="gerantTables")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gerant;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="tables")
     */
    private $players;

    /**
     * @ORM\ManyToOne(targetEntity=Seance::class, inversedBy="tables")
     * @ORM\JoinColumn(nullable=false)
     */
    private $seance;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxPlayer;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getGameFree(): ?string
    {
        return $this->gameFree;
    }

    public function setGameFree(?string $gameFree): self
    {
        $this->gameFree = $gameFree;

        return $this;
    }

    public function getGerant(): ?User
    {
        return $this->gerant;
    }

    public function setGerant(?User $gerant): self
    {
        $this->gerant = $gerant;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(User $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
        }

        return $this;
    }

    public function removePlayer(User $player): self
    {
        $this->players->removeElement($player);

        return $this;
    }

    public function getSeance(): ?Seance
    {
        return $this->seance;
    }

    public function setSeance(?Seance $seance): self
    {
        $this->seance = $seance;

        return $this;
    }

    public function getMaxPlayer(): ?int
    {
        return $this->maxPlayer;
    }

    public function setMaxPlayer(int $maxPlayer): self
    {
        $this->maxPlayer = $maxPlayer;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
