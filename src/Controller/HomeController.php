<?php

namespace App\Controller;

use App\Entity\Tchat;
use App\Entity\UserAsso;
use App\Entity\UserProfil;

use App\Service\Discussion as DiscussionSer;

use App\Repository\UserRepository;
use App\Repository\UserProfilRepository;
use App\Repository\UserAssoRepository;



use App\Repository\ActuRepository;
use App\Repository\TchatRepository;
use App\Repository\SeanceRepository;
use App\Repository\SondageRepository;

use App\Form\TchatType as TchatForm;
use App\Form\SeancePresence1Type as SeancePresenceForm1;
use App\Form\SeancePresence2Type as SeancePresenceForm2;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/", name="home")
 */
class HomeController extends AbstractController
{
	const AFFICHAGE_MAX_SEANCE = 2;

	/**
	 * @Route("/", name="")
	 */
	public function index(
		Request $request,
		ActuRepository $ar,
		TchatRepository $tr,
		SeanceRepository $ser,
		SondageRepository $sr,
		DiscussionSer $discussionSer,
		AuthenticationUtils $authenticationUtils
	){
		// Discussions
		$discussionSer->update();

		// Séances
		$seances = $this->seances($ser->getOldSeance(self::AFFICHAGE_MAX_SEANCE), $ser->getNextSeance(self::AFFICHAGE_MAX_SEANCE));
		$forms_seances = $this->seancesForm($seances, $ser, $request);

		// Tchat
		$form_tchat = $this->tchatForm($tr, $request);

		// Form valid
		if ($forms_seances === false || $form_tchat === false){ return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER); }

		return $this->render('home/index.html.twig',[

			// Tchat
			'tchats' => $tr->getLastTchats(),
			'form_tchat' => $form_tchat->createView(),

			// Séances
			'seances' => $seances,
			'form1' => $forms_seances[0]->createView(),
			'form2' => $forms_seances[1]->createView(),

			// Actus
			'actus' => $ar->findBy(['valid' => true], ['id' => 'DESC'], 3, 0),

			// Sondage
			'request' => $request,
			'sondages' => $sr->getSondageRunning(),

			// Calendrier
			'dateJour' => ucfirst($this->dateToFrench('now', 'l j F Y')),

			// Authentification
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

	/**
	 * Gère les formulaires d'inscription aux séances
	 */
	public function seancesForm($seances, $ser, $request) 
	{
		$user = $this->getUser();

		/* -- Form 1 -- */
		$seance = !empty($seances) ? $seances[array_key_first($seances)] : null;
		$form1 = $this->createForm(SeancePresenceForm1::class, $seance);
		$form1->handleRequest($request);

		if ($form1->isSubmitted() && $form1->isValid()){
			$user->inSeance($seance)
				? $seance->removePresent($user)
				: $seance->addPresent($user)
			;
			$ser->add($seance, true);

			return false;
		}

		/* -- Form 2 -- */
		$seance = !empty($seances) ? $seances[array_key_last($seances)] : null;

		$form2 = $this->createForm(SeancePresenceForm2::class, $seance);
		$form2->handleRequest($request);

		if ($form2->isSubmitted() && $form2->isValid()){
			$user->inSeance($seance)
				? $seance->removePresent($user)
				: $seance->addPresent($user)
			;
			$ser->add($seance, true);

			return false;
		}

		return [$form1, $form2];
	}

	/**
	 * Mini-tchat Ajout de contenu
	 */
	public function tchatForm($tr, $request) 
	{
		$tchat = new Tchat();
		$user = $this->getUser();


		$form = $this->createForm(TchatForm::class, $tchat);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid() && $user != null){

			$tchat
				->setDate(new \Datetime('now'))
				->setUser($user)
			;

			$tr->add($tchat, true);

			return false;
		}

		return $form;
	}

	/**
	 * @Route("/newuser", name="_newuser")
	 */
	public function newuser(
		UserRepository $ur,
		UserProfilRepository $up,
		UserAssoRepository $ua
	){
		$users = $ur->findAll();

		foreach ($users as $user){

			$userProfil = new UserProfil();
			$userProfil
				->setNom($user->getNom())
				->setPrenom($user->getPrenom())
				->setMail($user->getMail())
				->setAdresse($user->getAdresse())
				->setTelephone($user->getTelephone())
				->setUser($user)
			;
			$up->add($userProfil, true);

			$userAsso = new UserAsso();
			$userAsso
				->setDroitImage($user->getDroitImage())
				->setAdherant((int) $user->getAdherant())
				->setDateInscription($user->getDateInscription())
				->setDateFinAdhesion($user->getDateFinAdhesion())
				->setNotoriete($user->getNotoriete())
				->setRoleCa($user->getRoleCa())
				->setDateFinMandat($user->getDateFinMandat())
				->setMembreHonneur($user->getMembreHonneur() == null ? false : $user->getMembreHonneur())
				->setUser($user)
			;
			$ua->add($userAsso, true);
		}

		dump('ok');
		die;
	}
}
