<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
	/**
	 * @Route("/", name="home")
	 */
	public function index(Request $request)
	{
		$dateJour = new \DateTime('now');
		setlocale(LC_TIME, 'fr_CA.UTF-8');
		date_default_timezone_set('Europe/Paris');

		return $this->render('home/index.html.twig',[
			'dateJour' => $this->translate_date($dateJour->format('D')).' '.$dateJour->format('d').' '.strftime("%B").' '.$dateJour->format('Y'),
			'prochaineSeance' => $this->translate_date($dateJour->format('D')).' '.$dateJour->format('d').' '.strftime("%B").' '.$dateJour->format('Y'),
		]);
	}

	/**
	 * Traduit le jour de la semaine
	 */
	function translate_date($untranslated_date)
	{
		switch ($untranslated_date)
		{
			case "Mon":
				$translated_date = "lundi";
				break;
				
			case "Tue":
				$translated_date = "mardi";
				break;
				
			case "Wed":
				$translated_date = "mercredi";
				break;
				
			case "Thu":
				$translated_date = "jeudi";
				break;
				
			case "Fri":
				$translated_date = "vendredi";
				break;
				
			case "Sat":
				$translated_date = "samedi";
				break;
				
			case "Sun":
				$translated_date = "dimanche";
				break;

			default:
				$translated_date = "";
				break;
		}

		return $translated_date;
	}
}
