<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PhotosController extends AbstractController
{
	/**
	 * @Route("/photos", name="photos")
	 */
	public function index(Request $request)
	{

		return $this->render('photos/index.html.twig');
	}
}
