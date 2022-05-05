<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
	/**
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messages")
	 */
	private $user;

	/**
	 * @ORM\OneToOne(targetEntity=Message::class, cascade={"persist", "remove"})
	 */
	private $parent;

	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $ip;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $libelle;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $description;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $lu = false;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $date;

	public function getId(): ?int
	{
		return $this->id;
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

	public function getParent(): ?self
	{
		return $this->parent;
	}

	public function setParent(?self $parent): self
	{
		$this->parent = $parent;

		return $this;
	}

	public function getIp(): ?string
	{
		return $this->ip;
	}

	public function setIp(?string $ip): self
	{
		$this->ip = $ip;

		return $this;
	}

	public function getLibelle(): ?string
	{
		return $this->libelle;
	}

	public function setLibelle(?string $libelle): self
	{
		$this->libelle = $libelle;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(string $description): self
	{
		$this->description = $description;

		return $this;
	}

	public function getLu(): ?bool
	{
		return $this->lu;
	}

	public function setLu(bool $lu): self
	{
		$this->lu = $lu;

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
