<?php

namespace App\Entity;

use App\Repository\SondageUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SondageUserRepository::class)
 */
class SondageUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Sondage::class, inversedBy="votants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sondage;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sondages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $votant;

    /**
     * @ORM\Column(type="integer")
     */
    private $vote;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSondage(): ?Sondage
    {
        return $this->sondage;
    }

    public function setSondage(?Sondage $sondage): self
    {
        $this->sondage = $sondage;

        return $this;
    }

    public function getVotant(): ?User
    {
        return $this->votant;
    }

    public function setVotant(?User $votant): self
    {
        $this->votant = $votant;

        return $this;
    }

    public function getVote(): ?int
    {
        return $this->vote;
    }

    public function setVote(int $vote): self
    {
        $this->vote = $vote;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
