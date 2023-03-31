<?php

namespace App\Controller\Asso;

use App\Entity\Mandat;
use App\Entity\Organigramme;

use App\Repository\UserRepository;
use App\Repository\MandatRepository;
use App\Repository\OrganigrammeRepository;

use App\Form\OrgaType;

use App\Service\Log;
use App\Service\FileUploader;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/organigramme", name="orga")
 */
class OrgaController extends AbstractController
{
	// File upload
	private $file_uploader;

	// Repository
	private $or;
	private $mr;
	private $ur;

	public function __construct(
		FileUploader $file_uploader,
		OrganigrammeRepository $or,
		MandatRepository $mr,
		UserRepository $ur
	){
		$this->or = $or;
		$this->mr = $mr;
		$this->ur = $ur;
		$this->file_uploader = $file_uploader;
	}

	/**
	 * @Route("/", name="")
	 */
	public function index()
	{
		// Initialise
		$date_now = new \Datetime('now');
		$orgas = $this->or->findBy(['isActif' => true], ['id' => 'ASC']);
		$mandats = $this->mr->findBy(['isActif' => true, 'required' => true], ['priorite' => 'ASC']);
		$mandataires = $this->ur->mandataires();

		// Update orgas + Retire les mandats requis déja présents
		foreach($orgas as $key => $orga){

			if ($orga->getUSer() != null){

				$user_asso = $orga->getUser()->getAsso();

				// Set new DateFinMandat
				$orga = $this->setDateStartEnd($orga, $user_asso->getDateFinMandat());
				$this->or->add($orga, true);
				$orgas[$key] = $orga;

				// Set inactif
				if (
					// Dépassement de la date de fin du mandat
					$date_now > $orga->getEnd() ||

					// Sans/mauvais mandat
					$user_asso->getMandat() != $orga->getMandat() ||
					$user_asso->getMandat() == null
				){
					$orga->setIsActif(false);
					$this->or->add($orga, true);
					unset($orgas[$key]);
				}
			}

			// Retire les mandats requis déja présents
			foreach($mandats as $key2 => $mandat){
				if ($orga->getMandat() == $mandat){ unset($mandats[$key2]); }
			}
		}

		// Rajoute les orgas requis non présent
		foreach($mandats as $mandat){

			// Initialise
			$create = false;
			$mandataires = $mandat->getMandataire();

			// Récupère les mandataires
			foreach($mandataires as $mandataire){

				$create = true;
				$orga = new Organigramme();
				$dateFinMandat = $mandataire->getDateFinMandat();

				if ($date_now < $dateFinMandat){
					$orga->setUser($mandataire->getUser());
				}
				
				$orga->setMandat($mandat);
				$orga = $this->setDateStartEnd($orga, $dateFinMandat);
				$this->or->add($orga, true);
				$orgas[] = $orga;
			}

			// Si pas de mandataire
			if (!$create){
				$orga = new Organigramme();
				$orga->setMandat($mandat);
				$this->or->add($orga, true);
				$orgas[] = $orga;
			}
		}

		// Vérifie que les mandataires ont bien un orga, add ou edit si orga sans user
		foreach($mandataires as $key => $mandataire){

			$getOrga = false;
			$mandataire_asso = $mandataire->getAsso();
			$mandataire_mandat = $mandataire_asso->getMandat();
			$mandataire_mandat_dateFin = $mandataire_asso->getDateFinMandat();

			foreach($orgas as $key2 => $orga){

				// Orga existant
				if($orga->getUser() == $mandataire){
					$getOrga = true;

				// Orga sans user existant
				} elseif($orga->getUser() == null && $orga->getMandat() == $mandataire_mandat){
					$getOrga = true;
					$or = $orgas[$key2];
					$or->setUser($mandataire);
					$or = $this->setDateStartEnd($or, $mandataire_mandat_dateFin);
					$this->or->add($or, true);
					$orgas[$key2] = $or;
				}
			}

			// Pas d'orga
			if(!$getOrga){
				$or = new Organigramme();
				$or->setMandat($mandataire_mandat)->setUser($mandataire);
				$or = $this->setDateStartEnd($or, $mandataire_mandat_dateFin);
				$this->or->add($or, true);
				$orgas[] = $or;
			}
		}

		// Clean les orga inactif sans user
		$this->or->cleanUseless();

		// Trie par ordre de priorité
		$orgas = $this->orgaByPrio($orgas);

		return $this->render('asso/organigramme/index.html.twig', [
			'orgas' => $orgas,
		]);
	}

	/**
	 * @Route("/old", name="_old")
	 */
	public function old()
	{
		// Initialise
		$orgas = $this->or->findBy(['isActif' => false], ['id' => 'ASC']);

		// Clean les orga inactif sans user
		$this->or->cleanUseless();

		// Trie par ordre de priorité
		$orgas = $this->orgaByPrio($orgas);

		return $this->render('asso/organigramme/index.html.twig', [
			'old' => true,
			'orgas' => $orgas,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/{id}/edit", name="_edit")
	 */
	public function edit(Organigramme $orga, Request $request)
	{
		// User required
		if ($orga->getUser() == null){
			$this->addFlash('error', "Un organigramme sans mandataire ne peut pas être modifié.");
			return $this->redirectToRoute('orga', [], Response::HTTP_SEE_OTHER);
		}

		$form = $this->createForm(OrgaType::class, $orga, ['nb_orga' => count($this->or->findAll())]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid() && $this->formControl($orga, true)){

			$file = $form['photo']->getData();
			$delPhoto = $form['deletePhoto']->getData();

			// Photo
			if ($file != null && !$delPhoto){

				$file_name = $this->file_uploader->upload($file, 'orga');

				if (null !== $file_name){
					$orga->setPhoto($file_name);
					$this->or->add($orga, true);
					return $this->redirectToRoute('orga', [], Response::HTTP_SEE_OTHER);

				} else {
					$this->addFlash('error', "Erreur d'upload de l'image.");
				}

			} else {
				if ($delPhoto){ $orga->setPhoto(null); }
				$this->or->add($orga, true);
				return $this->redirectToRoute('orga', [], Response::HTTP_SEE_OTHER);
			}
		}

		return $this->renderForm('asso/organigramme/edit.html.twig', [
			'form' => $form,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/{id}/delete", name="_delete")
	 */
	public function delete(Request $request, Organigramme $orga)
	{
		if ($this->isCsrfTokenValid('delete'.$orga->getId(), $request->request->get('_token'))){
			$this->or->remove($orga, true);
		}

		return $this->redirectToRoute('orga', [], Response::HTTP_SEE_OTHER);
	}

	/**
	 * Contrôle du formulaire
	 */
	public function formControl($orga, $edit = false)
	{
		// Utilisateur obligatoire
		if (null == $orga->getUser()){
			$this->addFlash('error', "Un utilisateur est nécessaire.");
			return false;
		}

		// Utilisateur avec mandat nécessaire
		if (null == $orga->getUser()->getAsso()->getMandat()){
			$this->addFlash('error', "Un utilisateur avec un mandat actif est nécessaire.");
			return false;
		}

		// Utilisateur avec déjà un orga actif
		if (!$edit && count($this->or->findBy(['user' => $orga->getUser()->getId(), 'isActif' => true])) > 0){
			$this->addFlash('error', "Cette utilisateur a déjà un organigramme actif.");
			return false;
		}

		return true;
	}

	/**
	 * Trie les organigramme par ordre de priorité
	 */
	public function setDateStartEnd($orga, $dateFinMandat)
	{
		if ($dateFinMandat != null){

			// Date
			$dateDebutMandat = clone $dateFinMandat;
			$dateDebutMandat->modify('-'.$orga->getMandat()->getDuree().' years');

			$orga->setStart($dateDebutMandat);
			$orga->setEnd($dateFinMandat);

		}
		return $orga;
	}

	/**
	 * Trie les organigramme par ordre de priorité
	 */
	public function orgaByPrio($orgas)
	{
		//  Initialise
		$orders = [];
		$order2 = [];

		// Organise par priorité
		foreach($orgas as $orga){
			$orga->getUser() != null
				? $orders[$orga->getMandat()->getPriorite()][$orga->getUser()->getUsername()] = $orga
				: $orders[$orga->getMandat()->getPriorite()][] = $orga
			;
		}

		// Order by key
		ksort($orders);

		// Order sub by key
		foreach($orders as $key => $order){
			ksort($orders[$key]);
		}

		// Réorganise
		foreach($orders as $order){
			foreach($order as $sub_order){
				$order2[] = $sub_order;
			}
		}

		return $order2;
	}
}
