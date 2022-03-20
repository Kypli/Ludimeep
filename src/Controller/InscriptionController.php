<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InscriptionController extends AbstractController
{
	/**
	 * @Route("/inscription", name="inscription")
	 */
	public function index(Request $request)
	{

		return $this->render('inscription/index.html.twig');
	}
}
