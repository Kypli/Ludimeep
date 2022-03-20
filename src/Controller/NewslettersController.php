<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NewslettersController extends AbstractController
{
	/**
	 * @Route("/newsletters", name="newsletters")
	 */
	public function index(Request $request)
	{

		return $this->render('newsletters/index.html.twig');
	}
}
