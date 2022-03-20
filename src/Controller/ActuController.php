<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ActuController extends AbstractController
{
	/**
	 * @Route("/actu", name="actu")
	 */
	public function index(Request $request)
	{

		return $this->render('actu/index.html.twig');
	}
}
