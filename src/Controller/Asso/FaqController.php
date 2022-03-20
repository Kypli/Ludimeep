<?php

namespace App\Controller\Asso;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FaqController extends AbstractController
{
	/**
	 * @Route("/faq", name="faq")
	 */
	public function index(Request $request)
	{

		return $this->render('asso/faq/index.html.twig');
	}
}
