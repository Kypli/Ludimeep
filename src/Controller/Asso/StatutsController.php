<?php

namespace App\Controller\Asso;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatutsController extends AbstractController
{
	/**
	 * @Route("/statuts", name="statuts")
	 */
	public function index(Request $request)
	{

		return $this->render('asso/statuts/index.html.twig');
	}
}
