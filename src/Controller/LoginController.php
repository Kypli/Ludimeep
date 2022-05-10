<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
	/**
	 * @Route("/login", name="login")
	 */
	public function index(AuthenticationUtils $authenticationUtils): Response
	{
		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		if ($error != null){
			$this->addFlash('login_error', 'Email ou Mot de passe incorrect !');
		} else {
			$this->addFlash('login_info', 'Déconnexion effectuée !');
		}

		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->redirectToRoute('home');
	}
}
