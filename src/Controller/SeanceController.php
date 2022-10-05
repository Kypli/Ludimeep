<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Entity\SeanceType;
use App\Entity\SeanceLieu;

use App\Repository\SeanceRepository;
use App\Repository\SeanceTypeRepository;
use App\Repository\SeanceLieuRepository;

use App\Form\SeanceType as SeanceForm;
use App\Form\SeanceTypeType as SeanceTypeForm;
use App\Form\SeanceLieuType as SeanceLieuForm;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/seance", name="seance")
 */
class SeanceController extends AbstractController
{
	/**
	 * @Route("/", name="", methods={"GET"})
	 */
	public function index(SeanceRepository $sr): Response
	{
		return $this->render('seance/index.html.twig', [
			'seances_running' => $sr->getDateRunning([], ['date' => 'ASC']),
			'seances_over' => $sr->getDateOver([], ['date' => 'ASC']),
		]);
	}

	/**
	 * @Route("/add", name="_add", methods={"GET", "POST"})
	 */
	public function add(Request $request, SeanceRepository $sr): Response
	{
		$seance = new Seance();
		$form = $this->createForm(SeanceForm::class, $seance, ['edit' => false]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$sr->add($seance, true);

			return $this->redirectToRoute('seance', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('seance/add.html.twig', [
			'seance' => $seance,
			'form' => $form,
		]);
	}

	/**
	 * @Route("/{id}/edit", name="_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, Seance $seance, SeanceRepository $sr): Response
	{
		$form = $this->createForm(SeanceForm::class, $seance, ['edit' => true]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$sr->add($seance, true);

			return $this->redirectToRoute('seance', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('seance/edit.html.twig', [
			'seance' => $seance,
			'form' => $form,
		]);
	}

	/**
	 * @Route("/{id}", name="_delete", methods={"POST"})
	 */
	public function delete(Request $request, Seance $seance, SeanceRepository $sr): Response
	{
		if ($this->isCsrfTokenValid('delete'.$seance->getId(), $request->request->get('_token'))) {
			$sr->remove($seance, true);
		}

		return $this->redirectToRoute('seance', [], Response::HTTP_SEE_OTHER);
	}

	/**
	 * @Route("/type", name="_type", methods={"GET"})
	 */
	public function type(SeanceTypeRepository $str): Response
	{
		return $this->render('seance/type/index.html.twig', [
			'types' => $str->findAll(),
		]);
	}

	/**
	 * @Route("/type/add", name="_type_add", methods={"GET", "POST"})
	 */
	public function typeAdd(Request $request, SeanceTypeRepository $str): Response
	{
		$type = new SeanceType();
		$form = $this->createForm(SeanceTypeForm::class, $type);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$str->add($type, true);

			return $this->redirectToRoute('seance_type', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('seance/type/add.html.twig', [
			'type' => $type,
			'form' => $form,
		]);
	}

	/**
	 * @Route("/type/{id}/edit", name="_type_edit", methods={"GET", "POST"})
	 */
	public function typeEdit(Request $request, SeanceType $type, SeanceTypeRepository $str): Response
	{
		$form = $this->createForm(SeanceTypeForm::class, $type);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$str->add($type, true);

			return $this->redirectToRoute('seance_type', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('seance/type/edit.html.twig', [
			'type' => $type,
			'form' => $form,
		]);
	}

	/**
	 * @Route("/type/{id}", name="_type_delete", methods={"GET", "POST"})
	 */
	public function typeDelete(Request $request, SeanceType $type, SeanceTypeRepository $str): Response
	{
		if ($this->isCsrfTokenValid('delete'.$type->getId(), $request->request->get('_token'))) {
			$str->remove($type, true);
		}

		return $this->redirectToRoute('seance_type', [], Response::HTTP_SEE_OTHER);
	}

	/**
	 * @Route("/lieu", name="_lieu", methods={"GET"})
	 */
	public function lieu(SeanceLieuRepository $slr): Response
	{
		return $this->render('seance/lieu/index.html.twig', [
			'lieus' => $slr->findAll(),
		]);
	}

	/**
	 * @Route("/lieu/add", name="_lieu_add", methods={"GET", "POST"})
	 */
	public function lieuAdd(Request $request, SeanceLieuRepository $slr): Response
	{
		$lieu = new SeanceLieu();
		$form = $this->createForm(SeanceLieuForm::class, $lieu);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){

			// Désactive le défaut des autres lieu si le défaut est actif
			if ($lieu->isDefaut()){
				$lieux = $slr->findAll();
				foreach($lieux as $lieu_bis){
					if ($lieu_bis->getId() != $lieu->getId()){
						$lieu_bis->setDefaut(false);
						$slr->add($lieu_bis, true);
					}
				}
			}

			$slr->add($lieu, true);

			return $this->redirectToRoute('seance_lieu', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('seance/lieu/add.html.twig', [
			'lieu' => $lieu,
			'form' => $form,
		]);
	}

	/**
	 * @Route("/lieu/{id}/edit", name="_lieu_edit", methods={"GET", "POST"})
	 */
	public function lieuEdit(Request $request, SeanceLieu $lieu, SeanceLieuRepository $slr): Response
	{
		$form = $this->createForm(SeanceLieuForm::class, $lieu);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){

			// Désactive le défaut des autres lieu si le défaut est actif
			if ($lieu->isDefaut()){
				$lieux = $slr->findAll();
				foreach($lieux as $lieu_bis){
					if ($lieu_bis->getId() != $lieu->getId()){
						$lieu_bis->setDefaut(false);
						$slr->add($lieu_bis, true);
					}
				}
			}

			$slr->add($lieu, true);

			return $this->redirectToRoute('seance_lieu', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('seance/lieu/edit.html.twig', [
			'lieu' => $lieu,
			'form' => $form,
		]);
	}

	/**
	 * @Route("/lieu/{id}", name="_lieu_delete", methods={"GET", "POST"})
	 */
	public function lieuDelete(Request $request, SeanceLieu $lieu, SeanceLieuRepository $slr): Response
	{
		if ($this->isCsrfTokenValid('delete'.$lieu->getId(), $request->request->get('_token'))) {
			$slr->remove($lieu, true);
		}

		return $this->redirectToRoute('seance_lieu', [], Response::HTTP_SEE_OTHER);
	}
}
