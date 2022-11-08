<?php

namespace App\Service;

use App\Repository\OperationRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LigneComptable extends AbstractController
{
	private $or;

	public function __construct(OperationRepository $or)
	{
		$this->or = $or;
	}

	/**
	 * Met à jour les messages non lues en session
	 */
	public function update()
	{
		// Lignes en cours non validées (SESSION)
		$this->isGranted('ROLE_ADMIN')
			? $this->get('session')->set('solde_unvalid', $this->or->encours())
			: $this->get('session')->set('solde_unvalid', null)
		;

		return true;
	}
}
