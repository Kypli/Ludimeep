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
	public function login(AuthenticationUtils $authenticationUtils): Response
	{
	}

	/**
	 * @Route("/login_error", name="login_error")
	 */
	public function login_error(AuthenticationUtils $authenticationUtils): Response
	{
		// Get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		if ($error != null){

			$message = 
				$error->getMessage() == 'Bad credentials.' ||
				$error->getMessage() == 'The presented password is invalid.' ||
				$error->getMessage() == ''
					? "Login/Mot de passe incorrect !"
					: $error->getMessage()
			;

			$this->addFlash('login_error', $message);
		}

		// Last username entered by the user
		// $lastUsername = $authenticationUtils->getLastUsername();

		// return $this->render('login/index.html.twig', [
		// 	'last_username' => $lastUsername,
		// 	'error'         => $error,
		// ]);

		return $this->redirectToRoute('home');
	}

	/**
	 * @Route("/logout_alert", name="logout_alert")
	 */
	public function logout_alert(): Response
	{
		$this->addFlash('login_info', 'Déconnexion !');

		return $this->redirectToRoute('home');
	}
}
