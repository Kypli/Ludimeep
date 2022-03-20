<?php

namespace App\Controller\Contact;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppelController extends AbstractController
{
	/**
	 * @Route("/appel", name="appel")
	 */
	public function index(Request $request)
	{

		return $this->render('contact/appel/index.html.twig');
	}
}
