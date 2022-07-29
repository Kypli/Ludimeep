<?php

namespace App\Controller;

use App\Service\Discussion;

use App\Repository\ActuRepository;
use App\Repository\SeanceRepository;
use App\Repository\SondageRepository;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
	const AFFICHAGE_MAX_SEANCE = 2;

	/**
	 * @Route("/", name="home")
	 */
	public function index(
		AuthenticationUtils $authenticationUtils,
		Request $request,
		ActuRepository $ar,
		Discussion $discussionSer,
		SondageRepository $sr,
		SeanceRepository $ser
	){
		$discussionSer->update();

		return $this->render('home/index.html.twig',[
			'request' => $request,
			'user' => $this->getUser(),
			'sondages' => $sr->getSondageRunning(),
			'actus' => $ar->findBy(['valid' => true], ['id' => 'DESC'], 3, 0),
			'date' => new \Datetime(),
			'dateJour' => ucfirst($this->dateToFrench('now', 'l j F Y')),
			'seances' => $this->seances($ser->getOldSeance(self::AFFICHAGE_MAX_SEANCE), $ser->getNextSeance(self::AFFICHAGE_MAX_SEANCE)),
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

	/**
	 * Récupère les dates des prochaines séances
	 */
	public static function seances($olds, $nexts) 
	{
		foreach($olds as $key => $old){

			unset($dateTempo);
			$dateTempo = clone $old->getDate();

			$date_time = $old->getDate()->modify("+".$old->getDuree()->format('H').' hour +'.$old->getDuree()->format('i').'minute');

			if ($date_time <= new \Datetime('now')){
				unset($olds[$key]);
			} else {
				unset($nexts[array_key_last($nexts)]);
				$old->setDate($dateTempo);
			}
		}

		return array_merge($olds, $nexts);
	}
}
