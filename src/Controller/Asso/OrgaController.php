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

	public function __construct(FileUploader $file_uploader, OrganigrammeRepository $or, MandatRepository $mr, UserRepository $ur)
	{
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
		// TODO Récupérer les rôles CA indispensable et non occupé par un user pour les créer artificiellement
		$orga_vide = [];

		return $this->render('asso/organigramme/index.html.twig', [
			'orgas_vide' => $orga_vide,
			'orgas' => $this->or->findBy(['isActif' => true], ['id' => 'ASC']),
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
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/delete", name="_delete")
	 */
	public function delete(Request $request)
	{

		return $this->render('asso/organigramme/index.html.twig');
	}
}
