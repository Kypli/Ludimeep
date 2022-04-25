<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhotoRepository::class)
 */
class Photo
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
	private $name;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $alt;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $description;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $date;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $valid = true;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="photos")
	 */
	private $user;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="photosLanceurAlerte")
	 */
	private $lanceurAlerte;

	/**
	 * @ORM\OneToMany(targetEntity=CommentPhoto::class, mappedBy="photo")
	 */
	private $comments;


	public function __construct()
	{
		$this->comments = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getAlt(): ?string
	{
		return $this->alt;
	}

	public function setAlt(string $alt): self
	{
		$this->alt = $alt;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): self
	{
		$this->description = $description;

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

	public function getValid(): ?bool
	{
		return $this->valid;
	}

	public function setValid(bool $valid): self
	{
		$this->valid = $valid;

		return $this;
	}

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(?User $user): self
	{
		$this->user = $user;

		return $this;
	}

	public function getLanceurAlerte(): ?User
	{
		return $this->lanceurAlerte;
	}

	public function setLanceurAlerte(?User $lanceurAlerte): self
	{
		$this->lanceurAlerte = $lanceurAlerte;

		return $this;
	}

	/**
	 * @return Collection<int, Comment>
	 */
	public function getComments(): Collection
	{
		return $this->comments;
	}

	public function addComment(CommentPhoto $comment): self
	{
		if (!$this->comments->contains($comment)) {
			$this->comments[] = $comment;
			$comment->setActu($this);
		}

		return $this;
	}

	public function removeComment(CommentPhoto $comment): self
	{
		if ($this->comments->removeElement($comment)) {
			// set the owning side to null (unless already changed)
			if ($comment->getPhoto() === $this) {
				$comment->setPhoto(null);
			}
		}

		return $this;
	}
}
