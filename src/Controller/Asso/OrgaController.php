<?php

namespace App\Controller\Asso;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrgaController extends AbstractController
{
	/**
	 * @Route("/organigramme", name="organigramme")
	 */
	public function index(Request $request)
	{

		return $this->render('asso/organigramme/index.html.twig');
	}
}
