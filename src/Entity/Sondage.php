<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\SondageUser;

use Doctrine\ORM\Mapping as ORM;

use App\Repository\SondageRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=SondageRepository::class)
 */
class Sondage
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $line1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $line2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $line3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $line4;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $line5;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $line6;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $line7;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $line8;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end;

    /**
     * @ORM\OneToMany(targetEntity=SondageUser::class, mappedBy="sondage")
     */
    private $votants;

    public function __construct()
    {
        $this->votants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLine1(): ?string
    {
        return $this->line1;
    }

    public function setLine1(string $line1): self
    {
        $this->line1 = $line1;

        return $this;
    }

    public function getLine2(): ?string
    {
        return $this->line2;
    }

    public function setLine2(string $line2): self
    {
        $this->line2 = $line2;

        return $this;
    }

    public function getLine3(): ?string
    {
        return $this->line3;
    }

    public function setLine3(?string $line3): self
    {
        $this->line3 = $line3;

        return $this;
    }

    public function getLine4(): ?string
    {
        return $this->line4;
    }

    public function setLine4(?string $line4): self
    {
        $this->line4 = $line4;

        return $this;
    }

    public function getLine5(): ?string
    {
        return $this->line5;
    }

    public function setLine5(?string $line5): self
    {
        $this->line5 = $line5;

        return $this;
    }

    public function getLine6(): ?string
    {
        return $this->line6;
    }

    public function setLine6(?string $line6): self
    {
        $this->line6 = $line6;

        return $this;
    }

    public function getLine7(): ?string
    {
        return $this->line7;
    }

    public function setLine7(?string $line7): self
    {
        $this->line7 = $line7;

        return $this;
    }

    public function getLine8(): ?string
    {
        return $this->line8;
    }

    public function setLine8(?string $line8): self
    {
        $this->line8 = $line8;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(?\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getVotants(): Collection
    {
        return $this->votants;
    }

    public function addVotant(User $votant): self
    {
        if (!$this->votants->contains($votant)){
            $this->votants[] = $votant;
        }

        return $this;
    }

    public function removeVotant(User $votant): self
    {
        $this->votants->removeElement($votant);

        return $this;
    }

    public function voted($user_id): bool
    {
        foreach($this->votants as $value){
            if ($value->getVotant()->getId() == $user_id){
                return true;
            }
        }

        return false;
    }
}
