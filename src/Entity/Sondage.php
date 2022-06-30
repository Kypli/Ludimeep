<?php

namespace App\Entity;

use App\Repository\SondageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="integer")
     */
    private $result1 = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $result2 = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $result3;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $result4;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $result5;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $result6;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $result7;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $result8;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_debut;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_fin;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="sondages")
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

    public function getResult1(): ?int
    {
        return $this->result1;
    }

    public function setResult1(int $result1): self
    {
        $this->result1 = $result1;

        return $this;
    }

    public function getResult2(): ?int
    {
        return $this->result2;
    }

    public function setResult2(int $result2): self
    {
        $this->result2 = $result2;

        return $this;
    }

    public function getResult3(): ?int
    {
        return $this->result3;
    }

    public function setResult3(?int $result3): self
    {
        $this->result3 = $result3;

        return $this;
    }

    public function getResult4(): ?int
    {
        return $this->result4;
    }

    public function setResult4(?int $result4): self
    {
        $this->result4 = $result4;

        return $this;
    }

    public function getResult5(): ?int
    {
        return $this->result5;
    }

    public function setResult5(?int $result5): self
    {
        $this->result5 = $result5;

        return $this;
    }

    public function getResult6(): ?int
    {
        return $this->result6;
    }

    public function setResult6(?int $result6): self
    {
        $this->result6 = $result6;

        return $this;
    }

    public function getResult7(): ?int
    {
        return $this->result7;
    }

    public function setResult7(?int $result7): self
    {
        $this->result7 = $result7;

        return $this;
    }

    public function getResult8(): ?int
    {
        return $this->result8;
    }

    public function setResult8(?int $result8): self
    {
        $this->result8 = $result8;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(?\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

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
        if (!$this->votants->contains($votant)) {
            $this->votants[] = $votant;
        }

        return $this;
    }

    public function removeVotant(User $votant): self
    {
        $this->votants->removeElement($votant);

        return $this;
    }
}
