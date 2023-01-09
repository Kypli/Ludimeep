<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
	/**
	 * @Route("/login", name="login", methods={"GET", "POST"})
	 */
	public function login(AuthenticationUtils $authenticationUtils)
	{
		// if ($this->getUser()){
		// 	return $this->redirectToRoute('home');
		// }

		// Get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		// Last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		// return $this->render('home/connexion/_index.html.twig', [
		// 	'last_username' => $lastUsername,
		// 	'error' => $error,
		// ]);
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

	/**
	 * @Route("/logout", name="app_logout")
	 */
	public function logout(): Response
	{
		throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
	}
}
