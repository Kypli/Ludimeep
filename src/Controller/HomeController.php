<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
	/**
	 * @Route("/", name="home")
	 */
	public function index(AuthenticationUtils $authenticationUtils, Request $request)
	{
		// Login
		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render('home/index.html.twig',[
			'dateJour' => ucfirst($this->dateToFrench('now', 'l j F Y')),
			'last_username' => $lastUsername,
			'error' => $error,
		]);
	}

	/**
	 * Crée et traduit une date en Français
	 */
	public static function dateToFrench($date, $format) 
	{
		$english_days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
		$french_days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
		$english_months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		$french_months = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];

		return str_replace(
			$english_months,
			$french_months,
			str_replace(
				$english_days,
				$french_days,
				date($format, strtotime($date))
			)
		);
	}
}
