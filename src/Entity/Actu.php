<?php

namespace App\Entity;

use App\Repository\ActuRepository;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=ActuRepository::class)
 */
class Actu
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="actus", cascade={"persist"})
	 */
	private $auteur;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $date;

	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $titre;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $text1;

	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $text1Class;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $text2;

	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $text2Class;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $text3;

	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $text3Class;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $text4;

	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $text4Class;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $text5;

	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $text5Class;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $text6;

	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $text6Class;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $photo1;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $photo1Alt;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $photo2;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $photo2Alt;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $photo3;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $photo3Alt;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	private $ordre;

	/**
	 * @ORM\OneToMany(targetEntity=CommentActu::class, mappedBy="actu")
	 */
	private $comments;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $valid = false;

	public function __construct()
	{
		$this->comments = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getAuteur(): ?user
	{
		return $this->auteur;
	}

	public function setAuteur(?user $auteur): self
	{
		$this->auteur = $auteur;

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

	public function getTitre(): ?string
	{
		return $this->titre;
	}

	public function setTitre(?string $titre): self
	{
		$this->titre = $titre;

		return $this;
	}

	public function getText1(): ?string
	{
		return $this->text1;
	}

	public function setText1(string $text1): self
	{
		$this->text1 = $text1;

		return $this;
	}

	public function getText1Class(): ?string
	{
		return $this->text1Class;
	}

	public function setText1Class(?string $text1Class): self
	{
		$this->text1Class = $text1Class;

		return $this;
	}

	public function getText2(): ?string
	{
		return $this->text2;
	}

	public function setText2(?string $text2): self
	{
		$this->text2 = $text2;

		return $this;
	}

	public function getText2Class(): ?string
	{
		return $this->text2Class;
	}

	public function setText2Class(?string $text2Class): self
	{
		$this->text2Class = $text2Class;

		return $this;
	}

	public function getText3(): ?string
	{
		return $this->text3;
	}

	public function setText3(?string $text3): self
	{
		$this->text3 = $text3;

		return $this;
	}

	public function getText3Class(): ?string
	{
		return $this->text3Class;
	}

	public function setText3Class(?string $text3Class): self
	{
		$this->text3Class = $text3Class;

		return $this;
	}

	public function getText4(): ?string
	{
		return $this->text4;
	}

	public function setText4(?string $text4): self
	{
		$this->text4 = $text4;

		return $this;
	}

	public function getText4Class(): ?string
	{
		return $this->text4Class;
	}

	public function setText4Class(?string $text4Class): self
	{
		$this->text4Class = $text4Class;

		return $this;
	}

	public function getText5(): ?string
	{
		return $this->text5;
	}

	public function setText5(?string $text5): self
	{
		$this->text5 = $text5;

		return $this;
	}

	public function getText5Class(): ?string
	{
		return $this->text5Class;
	}

	public function setText5Class(?string $text5Class): self
	{
		$this->text5Class = $text5Class;

		return $this;
	}

	public function getText6(): ?string
	{
		return $this->text6;
	}

	public function setText6(?string $text6): self
	{
		$this->text6 = $text6;

		return $this;
	}

	public function getText6Class(): ?string
	{
		return $this->text6Class;
	}

	public function setText6Class(?string $text6Class): self
	{
		$this->text6Class = $text6Class;

		return $this;
	}

	public function getPhoto1(): ?string
	{
		return $this->photo1;
	}

	public function setPhoto1(?string $photo1): self
	{
		$this->photo1 = $photo1;

		return $this;
	}

	public function getPhoto1Alt(): ?string
	{
		return $this->photo1Alt;
	}

	public function setPhoto1Alt(?string $photo1Alt): self
	{
		$this->photo1Alt = $photo1Alt;

		return $this;
	}

	public function getPhoto2(): ?string
	{
		return $this->photo2;
	}

	public function setPhoto2(?string $photo2): self
	{
		$this->photo2 = $photo2;

		return $this;
	}

	public function getPhoto2Alt(): ?string
	{
		return $this->photo2Alt;
	}

	public function setPhoto2Alt(?string $photo2Alt): self
	{
		$this->photo2Alt = $photo2Alt;

		return $this;
	}

	public function getPhoto3(): ?string
	{
		return $this->photo3;
	}

	public function setPhoto3(?string $photo3): self
	{
		$this->photo3 = $photo3;

		return $this;
	}

	public function getPhoto3Alt(): ?string
	{
		return $this->photo3Alt;
	}

	public function setPhoto3Alt(?string $photo3Alt): self
	{
		$this->photo3Alt = $photo3Alt;

		return $this;
	}

	public function getOrdre(): ?string
	{
		return $this->ordre;
	}

	public function setOrdre(string $ordre): self
	{
		$this->ordre = $ordre;

		return $this;
	}

	/**
	 * @return Collection<int, Comment>
	 */
	public function getComments(): Collection
	{
		return $this->comments;
	}

	public function addComment(CommentActu $comment): self
	{
		if (!$this->comments->contains($comment)) {
			$this->comments[] = $comment;
			$comment->setActu($this);
		}

		return $this;
	}

	public function removeComment(CommentActu $comment): self
	{
		if ($this->comments->removeElement($comment)) {
			// set the owning side to null (unless already changed)
			if ($comment->getActu() === $this) {
				$comment->setActu(null);
			}
		}

		return $this;
	}

	public function getValid(): ?bool
	{
		return $this->valid;
	}

	public function setValid(bool $valid): self
	{
		$this->valid = $valid;

		return $this;
	}
}
