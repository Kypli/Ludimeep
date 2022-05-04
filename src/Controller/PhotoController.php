<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\PhotoType as PhotoForm;
use App\Service\FileUploader;
use App\Repository\PhotoRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/photo", name="photo")
 */
class PhotoController extends AbstractController
{

	private $file_uploader;

	public function __construct(FileUploader $file_uploader)
	{
		$this->file_uploader = $file_uploader;
	}

	/**
	 * @Route("/", name="_index", methods={"GET"})
	 */
	public function index(PhotoRepository $photoRepository): Response
	{
		return $this->render('photo/index.html.twig', [
			'photos' => $photoRepository->findAll(),
		]);
	}

	/**
	 * @IsGranted("ROLE_USER")
	 * @Route("/add", name="_add", methods={"GET", "POST"})
	 */
	public function add(Request $request, PhotoRepository $photoRepository): Response
	{
		// User sans droit d'ajout d'image
		if (!$this->getUser()->getAccesPhoto()){
			$this->addFlash('error', "Vous n'avez pas les droits pour poster une image.");
			return $this->redirectToRoute('photo_index', [], Response::HTTP_SEE_OTHER);
		}

		$photo = new Photo();
		$form = $this->createForm(PhotoForm::class, $photo);
		$form
			->remove('user')
			->remove('date')
		;
		$form->handleRequest($request);

		// Photo invalid
		if ($form->isSubmitted() && !$form->isValid()){
			$this->addFlash('error', "Seul des images de moins de 5M sont autorisées.");
		}

		// Form submit
		if ($form->isSubmitted() && $form->isValid()){

			// Date + auteur
			$photo
				->setUser($this->getUser())
				->setDate(new \Datetime('now'))
			;

			// Photo
			$file = $form['name']->getData();

			// Add file photo
			if ($file){

				$file_name = $this->file_uploader->upload($file);

				if (null !== $file_name){
					// $directory = $this->file_uploader->getTargetDirectory();
					// $full_path = $directory.'/'.$file_name;

					$text = 'setName';
					$photo->$text($file_name);

				} else {
					$this->addFlash('error', "Erreur d'upload de l'image.");
				}

				$photoRepository->add($photo);
				return $this->redirectToRoute('photo_index', [], Response::HTTP_SEE_OTHER);

			// Pas de photo
			} else {
				$this->addFlash('error', "Vous devez choisir une image.");
			}
		}

		return $this->renderForm('photo/add.html.twig', [
			'photo' => $photo,
			'form' => $form,
		]);
	}

	/**
	 * @IsGranted("ROLE_USER")
	 * @Route("/{id}/edit", name="_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, Photo $photo, PhotoRepository $photoRepository): Response
	{
		// User sans droit d'ajout d'image
		if (!$this->getUser()->getAccesPhoto()){
			$this->addFlash('error', "Vous n'avez pas les droits pour poster une image.");
			return $this->redirectToRoute('photo_index', [], Response::HTTP_SEE_OTHER);
		}

		// User non propriétaire
		if (!$this->isGranted('ROLE_ADMIN') && $this->getUser()->getId() != $photo->getUser()->getId()){
			$this->addFlash('error', "Vous n'avez pas les droits pour modifier cette image.");
			return $this->redirectToRoute('photo_index', [], Response::HTTP_SEE_OTHER);
		}

		$form = $this->createForm(PhotoForm::class, $photo);

		// remove fields
		$form->remove('name');
		if(!$this->isGranted('ROLE_ADMIN')){
			$form
				->remove('user')
				->remove('date')
				->remove('name')
			;
		}

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){
			$photoRepository->add($photo);
			return $this->redirectToRoute('photo_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('photo/edit.html.twig', [
			'photo' => $photo,
			'form' => $form,
		]);
	}

	/**
	 * @IsGranted("ROLE_USER")
	 * @Route("/{id}", name="_delete", methods={"POST"})
	 */
	public function delete(Request $request, Photo $photo, PhotoRepository $photoRepository): Response
	{
		if (
			$this->isCsrfTokenValid('delete'.$photo->getId(), $request->request->get('_token')) &&
			(
				$this->isGranted('ROLE_ADMIN') ||
				$this->getUser()->getId() == $photo->getUser()->getId()
			)
		){
			$photoRepository->remove($photo);
			$this->addFlash('success', "La photo a bien été supprimée.");
		}

		return $this->redirectToRoute('photo_index', [], Response::HTTP_SEE_OTHER);
	}

	/**
	 * @IsGranted("ROLE_USER")
	 * @Route("/{id}/signale", name="_signale")
	 * Signale une photo et la rend invisible (sauf pour admin et propriétaire)
	 */
	public function signale(Request $request, Photo $photo, PhotoRepository $photoRepository): Response
	{
		if (
			$this->isGranted('ROLE_ADMIN') ||
			(
				$photo->getValid() &&
				$this->getUser()->getId() != $photo->getUser()->getId() &&
				$this->getUser()->getAccesPhotoLanceurAlerte()
			)
		){
			$photo->setLanceurAlerte($this->getUser());
			$photo->setValid(false);
			$photoRepository->add($photo);
		}

		return $this->redirectToRoute('photo_index', ['id' => $photo->getId()], Response::HTTP_SEE_OTHER);
	}

	/**
	 * @IsGranted("ROLE_USER")
	 * @Route("/{id}/revalid", name="_revalid")
	 * Revalide une photo et la rend visible
	 */
	public function revalid(Request $request, Photo $photo, PhotoRepository $photoRepository): Response
	{
		if (
			$this->isGranted('ROLE_ADMIN') ||
			(
				!$photo->getValid() &&
				$this->getUser()->getId() == $photo->getLanceurAlerte()->getId()
			)
		){
			$photo->setLanceurAlerte(null);
			$photo->setValid(true);
			$photoRepository->add($photo);
		}

		return $this->redirectToRoute('photo_index', ['id' => $photo->getId()], Response::HTTP_SEE_OTHER);
	}
}
