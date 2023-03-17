<?php

namespace App\Entity;

use App\Repository\MandatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MandatRepository::class)
 */
class Mandat
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
    private $titre;

    /**
     * En année
     * @ORM\Column(type="integer")
     */
    private $duree;

    /**
     * @ORM\Column(type="integer")
     */
    private $priorite;

    /**
     * @ORM\Column(type="boolean")
     */
    private $required;

    /**
     * @ORM\Column(type="boolean")
     */
    private $uniq;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActif;

    /**
     * @ORM\OneToMany(targetEntity=UserAsso::class, mappedBy="mandat")
     */
    private $mandataires;

    /**
     * @ORM\OneToMany(targetEntity=Organigramme::class, mappedBy="mandat", orphanRemoval=true)
     */
    private $organigrammes;

    public function __construct()
    {
        $this->mandataires = new ArrayCollection();
        $this->organigrammes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getPriorite(): ?int
    {
        return $this->priorite;
    }

    public function setPriorite(int $priorite): self
    {
        $this->priorite = $priorite;

        return $this;
    }

    public function isRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function isUniq(): ?bool
    {
        return $this->uniq;
    }

    public function setUniq(bool $uniq): self
    {
        $this->uniq = $uniq;

        return $this;
    }

    public function isIsActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(bool $isActif): self
    {
        $this->isActif = $isActif;

        return $this;
    }

    /**
     * @return Collection<int, UserAsso>
     */
    public function getMandataire(): Collection
    {
        return $this->mandataires;
    }

    public function addMandataire(UserAsso $mandataire): self
    {
        if (!$this->mandataires->contains($mandataire)) {
            $this->mandataires[] = $mandataire;
            $mandataire->setMandat($this);
        }

        return $this;
    }

    public function removeMandataire(UserAsso $mandataire): self
    {
        if ($this->mandataires->removeElement($mandataire)) {
            // set the owning side to null (unless already changed)
            if ($mandataire->getMandat() === $this) {
                $mandataire->setMandat(null);
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
            $organigramme->setMandat($this);
        }

        return $this;
    }

    public function removeOrganigramme(Organigramme $organigramme): self
    {
        if ($this->organigrammes->removeElement($organigramme)) {
            // set the owning side to null (unless already changed)
            if ($organigramme->getMandat() === $this) {
                $organigramme->setMandat(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->titre;
    }
}
