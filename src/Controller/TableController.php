<?php

namespace App\Controller;

use App\Entity\Table;
use App\Form\TableType;
use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/table", name="table")
 */
class TableController extends AbstractController
{
	/**
	 * @Route("/", name="", methods={"GET"})
	 */
	public function index(TableRepository $tableRepository): Response
	{
		return $this->render('table/index.html.twig', [
			'tables' => $tableRepository->findAll(),
		]);
	}

	/**
	 * @Route("/add", name="_add", methods={"GET", "POST"})
	 */
	public function add(Request $request, TableRepository $tableRepository): Response
	{
		$table = new Table();
		$form = $this->createForm(TableType::class, $table);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$tableRepository->add($table, true);

			return $this->redirectToRoute('table_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('table/new.html.twig', [
			'table' => $table,
			'form' => $form,
		]);
	}

	/**
	 * @Route("/{id}", name="_show", methods={"GET"})
	 */
	public function show(Table $table): Response
	{
		return $this->render('table/show.html.twig', [
			'table' => $table,
		]);
	}

	/**
	 * @Route("/{id}/edit", name="_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, Table $table, TableRepository $tableRepository): Response
	{
		$form = $this->createForm(TableType::class, $table);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$tableRepository->add($table, true);

			return $this->redirectToRoute('table', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('table/edit.html.twig', [
			'table' => $table,
			'form' => $form,
		]);
	}

	/**
	 * @Route("/{id}", name="_delete", methods={"POST"})
	 */
	public function delete(Request $request, Table $table, TableRepository $tableRepository): Response
	{
		if ($this->isCsrfTokenValid('delete'.$table->getId(), $request->request->get('_token'))) {
			$tableRepository->remove($table, true);
		}

		return $this->redirectToRoute('table_index', [], Response::HTTP_SEE_OTHER);
	}
}
