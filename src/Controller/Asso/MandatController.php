<?php

namespace App\Controller\Asso;

use App\Entity\Mandat;
use App\Form\MandatType;
use App\Repository\MandatRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/mandat", name="mandat")
 */
class MandatController extends AbstractController
{
	/**
	 * @Route("/", name="")
	 */
	public function index(MandatRepository $mr): Response
	{
		return $this->render('asso/mandat/index.html.twig', [
			'mandats' => $mr->findAll(),
		]);
	}

	/**
	 * @Route("/add", name="_add")
	 */
	public function add(Request $request, MandatRepository $mr)
	{
		$mandat = new Mandat();
		$form = $this->createForm(MandatType::class, $mandat);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){

			$mr->add($mandat, true);

			return $this->redirectToRoute('mandat', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('asso/mandat/add.html.twig', [
			'form' => $form,
		]);
	}

	/**
	 * @Route("/{id}/edit", name="_edit")
	 */
	public function edit(Mandat $mandat, Request $request, MandatRepository $mr)
	{
		$form = $this->createForm(MandatType::class, $mandat);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){

			$mr->add($mandat, true);

			return $this->redirectToRoute('mandat', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('asso/mandat/add.html.twig', [
			'form' => $form,
		]);
	}
}
