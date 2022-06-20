<?php

namespace App\Controller;

use App\Service\Discussion;

use App\Repository\ActuRepository;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
	/**
	 * @Route("/", name="home")
	 */
	public function index(AuthenticationUtils $authenticationUtils, Request $request, ActuRepository $actuRepository, Discussion $discussionSer)
	{
		$discussionSer->update();

		return $this->render('home/index.html.twig',[
			'user' => $this->getUser(),
			'actus' => $actuRepository->findBy(['valid' => true], ['id' => 'DESC'], 3, 0),
			'dateJour' => ucfirst($this->dateToFrench('now', 'l j F Y')),
			'titre_connexion' => null !== $this->getUser() ? 'Mon espace' : 'Connexion',
			'error' => $authenticationUtils->getLastAuthenticationError(),		// get the login error if there is one
			'last_username' => $authenticationUtils->getLastUsername(),			// last username entered by the user
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
