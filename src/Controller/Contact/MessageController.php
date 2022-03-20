<?php

namespace App\Controller\Contact;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
	/**
	 * @Route("/message", name="message")
	 */
	public function index(Request $request)
	{

		return $this->render('contact/message/index.html.twig');
	}
}
