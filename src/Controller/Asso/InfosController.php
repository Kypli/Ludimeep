<?php

namespace App\Controller\Asso;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InfosController extends AbstractController
{
	/**
	 * @Route("/infos", name="infos")
	 */
	public function index(Request $request)
	{

		return $this->render('asso/infos/index.html.twig');
	}
}
