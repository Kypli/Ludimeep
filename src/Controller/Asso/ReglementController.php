<?php

namespace App\Controller\Asso;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReglementController extends AbstractController
{
	/**
	 * @Route("/reglement", name="reglement")
	 */
	public function index(Request $request)
	{

		return $this->render('asso/reglement/index.html.twig');
	}
}
