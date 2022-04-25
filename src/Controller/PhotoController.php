<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\PhotoType;
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
		$photo = new Photo();
		$form = $this->createForm(PhotoType::class, $photo);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid() && $this->getUser()->getAccesPhoto()){
			$photoRepository->add($photo);
			return $this->redirectToRoute('app_photo_index', [], Response::HTTP_SEE_OTHER);
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
		$form = $this->createForm(PhotoType::class, $photo);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$photoRepository->add($photo);
			return $this->redirectToRoute('app_photo_index', [], Response::HTTP_SEE_OTHER);
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
				$this->isGranted('ROLE_ADMIN') &&
				$this->getUser()->getId() == $photo->getUser()->getId()
			)
		){
			$this->addFlash('success', "La photo a bien été supprimée.");
			$photoRepository->remove($photo);
		}

		return $this->redirectToRoute('photo_index', [], Response::HTTP_SEE_OTHER);
	}

	/**
	 * @IsGranted("ROLE_USER")
	 * @Route("/{id}/signale", name="_signale")
	 * Signale une photo et la rend invisible (sauf admin et propriétaire)
	 */
	public function signale(Request $request, Photo $photo, PhotoRepository $photoRepository): Response
	{
		// Initialise
		$user_id = $this->getUser()->getId();
		$photo_user_id = $photo->getUser()->getId();
		$photo_valid = $photo->getValid();

		// Signale
		if (
			(
				$photo_valid &&
				$user_id != $photo_user_id &&
				$this->getUser()->getAccesPhotoLanceurAlerte()
			) ||
			$this->isGranted('ROLE_ADMIN')
		){
			$photo->setLanceurAlerte($this->getUser());
			$photo->setValid(false);
			$photoRepository->add($photo);
		}

		// Revalid
		if (
			(
				!$photo_valid &&
				$user_id == $photo->getLanceurAlerte()->getId()
			) ||
			$this->isGranted('ROLE_ADMIN')
		){
			$photo->setLanceurAlerte(null);
			$photo->setValid(true);
			$photoRepository->add($photo);
		}

		return $this->redirectToRoute('photo_index', ['id' => $photo->getId()], Response::HTTP_SEE_OTHER);
	}
}
