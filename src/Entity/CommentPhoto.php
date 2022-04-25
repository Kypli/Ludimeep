<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentRepository;

/**
 * @ORM\Entity(repositoryClass=CommentPhotoRepository::class)
 */
class CommentPhoto
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="text")
	 */
	private $text;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $date;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commentsPhoto")
	 */
	private $user;

	/**
	 * @ORM\ManyToOne(targetEntity=Photo::class, inversedBy="comments")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $photo;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getText(): ?string
	{
		return $this->text;
	}

	public function setText(string $text): self
	{
		$this->text = $text;

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

	public function getUser(): ?user
	{
		return $this->user;
	}

	public function setUser(?user $user): self
	{
		$this->user = $user;

		return $this;
	}

	public function getPhoto(): ?Photo
	{
		return $this->photo;
	}

	public function setPhoto(?Photo $photo): self
	{
		$this->photo = $photo;

		return $this;
	}
}
