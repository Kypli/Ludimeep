<?php

namespace App\Service;

use App\Repository\MessageRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Discussion extends AbstractController
{
	private $mr;

	public function __construct(MessageRepository $mr)
	{
		$this->mr = $mr;
	}

	/**
	 * Met à jour les messages non lues en session
	 */
	public function update()
	{
		// Messages non lues (SESSION)
		$this->getUser() != null
			? $this->get('session')->set('message_perso', $this->mr->messageNonLue($this->getUser()))
			: $this->get('session')->set('message_perso', null)
		;

		// Messages non lues pour admin (SESSION)
		$this->isGranted('ROLE_ADMIN')
			? $this->get('session')->set('message_admin', $this->mr->messageAdminNonLue($this->getUser()))
			: $this->get('session')->set('message_admin', null)
		;

		return true;
	}
}
