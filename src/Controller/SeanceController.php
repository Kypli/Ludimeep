<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Entity\SeanceType;

use App\Repository\SeanceRepository;
use App\Repository\SeanceTypeRepository;

use App\Form\SeanceType as SeanceForm;
use App\Form\SeanceTypeType as SeanceTypeForm;

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
}
