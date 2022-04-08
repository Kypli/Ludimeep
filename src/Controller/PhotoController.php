<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/photo", name="photo")
 */
class PhotoController extends AbstractController
{
	/**
	 * @Route("/", name="")
	 */
	public function index(Request $request)
	{

		return $this->render('photo/index.html.twig');
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/add", name="_add")
	 */
	public function add(Request $request)
	{

		return $this->render('photo/index.html.twig');
	}
}
