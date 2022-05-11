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
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messagesDestinateur")
	 */
	private $user;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messagesDestinataire")
	 */
	private $destinataire;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $discussion;

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

	public function getDestinataire(): ?User
	{
		return $this->destinataire;
	}

	public function setDestinataire(?User $destinataire): self
	{
		$this->destinataire = $destinataire;

		return $this;
	}

	public function getDiscussion(): ?int
	{
		return $this->discussion;
	}

	public function setDiscussion(?int $discussion): self
	{
		$this->discussion = $discussion;

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
