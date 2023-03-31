<?php

namespace App\Controller\Asso;

use App\Entity\Mandat;
use App\Entity\Organigramme;

use App\Repository\UserRepository;
use App\Repository\MandatRepository;
use App\Repository\OrganigrammeRepository;

use App\Form\OrgaType;
use App\Form\OrgaEditType;

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

			$keepKey = -1;
			$getOrga = false;

			foreach($orgas as $key2 => $orga){
				if($orga->getUser() == $mandataire){
					$getOrga = true;
				} elseif($orga->getUser() == null && $orga->getMandat() == $mandataire->getAsso()->getMandat()){
					$keepKey = $key2;
				}
			}
			if(!$getOrga){
				$or = $orgas[$keepKey];
				$or = $this->setDateStartEnd($or, $mandataire->getAsso()->getDateFinMandat());
				$or->setUser($mandataire);

				$this->or->add($or, true);
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
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/add", name="_add")
	 */
	public function add(Request $request)
	{
		$orga = new Organigramme();
		$form = $this->createForm(OrgaType::class, $orga, ['nb_orga' => count($this->or->findAll())]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid() && $this->formControl($orga)){

			// Init
			$user_asso = $orga->getUser()->getAsso();
			$mandat = $user_asso->getMandat();

			// Date
			$dateFinMandat = $user_asso->getDateFinMandat();
			$dateDebutMandat = clone $dateFinMandat;
			$dateDebutMandat->modify('-'.$mandat->getDuree().' years');

			// Photo
			$file = $form['photo']->getData();
			$file_name = $this->file_uploader->upload($file, 'orga');

			if (null !== $file_name){
				$text = 'setPhoto';
				$orga->$text($file_name);

				// Save
				$orga->setIsActif(true);
				$orga->setMandat($mandat);
				$orga->setStart($dateDebutMandat);
				$orga->setEnd($dateFinMandat);

				$this->or->add($orga, true);
				// $log->saveLog(Log::ORGA);

				return $this->redirectToRoute('orga', [], Response::HTTP_SEE_OTHER);

			} else {
				$this->addFlash('error', "Erreur d'upload de l'image.");
			}
		}

		return $this->renderForm('asso/organigramme/add.html.twig', [
			'edit' => false,
			'form' => $form,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/{id}/edit", name="_edit")
	 */
	public function edit(Organigramme $orga, Request $request)
	{
		$form = $this->createForm(OrgaEditType::class, $orga, ['nb_orga' => count($this->or->findAll())]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid() && $this->formControl($orga, true)){

			// Photo
			$file = $form['photo']->getData();
			$file_name = $this->file_uploader->upload($file, 'orga');

			if (null !== $file_name){
				$text = 'setPhoto';
				$orga->$text($file_name);

				// Save
				$orga->setStart($dateDebutMandat);
				$orga->setEnd($dateFinMandat);

				$this->or->add($orga, true);

				return $this->redirectToRoute('orga', [], Response::HTTP_SEE_OTHER);

			} else {
				$this->addFlash('error', "Erreur d'upload de l'image.");
			}
		}

		return $this->renderForm('asso/organigramme/add.html.twig', [
			'edit' => true,
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

		// Photo obligatoire
		if (null == $orga->getPhoto()){
			$this->addFlash('error', "Photo obligatoire.");
			return false;
		}

		return true;
	}

	/**
	 * Trie les organigramme par ordre de priorité
	 */
	public function setDateStartEnd($orga, $dateFinMandat)
	{
		// Date
		$dateDebutMandat = clone $dateFinMandat;
		$dateDebutMandat->modify('-'.$orga->getMandat()->getDuree().' years');

		$orga->setStart($dateDebutMandat);
		$orga->setEnd($dateFinMandat);

		return $orga;
	}

	/**
	 * Trie les organigramme par ordre de priorité
	 */
	public function orgaByPrio($orgas)
	{

		return $orgas;
	}
}
