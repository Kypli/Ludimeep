<?php

namespace App\Controller;

use App\Entity\Tchat;
use App\Entity\Table;
use App\Entity\UserAsso;
use App\Entity\UserProfil;

use App\Service\Discussion as DiscussionSer;

use App\Repository\UserRepository;
use App\Repository\UserProfilRepository;
use App\Repository\UserAssoRepository;

use App\Repository\ActuRepository;
use App\Repository\TchatRepository;
use App\Repository\TableRepository;
use App\Repository\SeanceRepository;
use App\Repository\SondageRepository;
use App\Repository\SeanceLieuRepository;

use App\Form\TchatType as TchatForm;
use App\Form\TableType as TableForm;
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
		TableRepository $tar,
		TchatRepository $tr,
		SeanceRepository $ser,
		SeanceLieuRepository $slr,
		SondageRepository $sr,
		DiscussionSer $discussionSer,
		AuthenticationUtils $authenticationUtils
	){
		// User
		$user = $this->getUser();

		// Discussions
		$discussionSer->update();

		// Séances
		$seances = $this->seances($ser->getOldSeance(self::AFFICHAGE_MAX_SEANCE), $ser->getNextSeance(SELF::AFFICHAGE_MAX_SEANCE));
		$forms_seances = $this->seancesForm($seances, $ser, $request, $user);

		// Tchat
		$form_tchat = $this->tchatForm($tr, $request, $user);

		// Table
		$seances_table = $this->seancesTable($ser, $user);
		$form_table = $this->tableForm($tar, $seances[0], $seances_table, $request, $user);

		// Form valid
		if (
			$forms_seances === false ||
			$form_tchat === false ||
			$form_table === false
		){ return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER); }

		return $this->render('home/index.html.twig',[

			// Authentification
			'error' => $authenticationUtils->getLastAuthenticationError(),		// get the login error if there is one
			'last_username' => $authenticationUtils->getLastUsername(),			// last username entered by the user

			// Tchat
			'tchats' => $tr->getLastTchats(),
			'form_tchat' => $form_tchat->createView(),

			// Tables
			'tables' => $tar->getCurrentTables(),
			'form_table' => $form_table->createView(),

			// Sondage
			'request' => $request,
			'sondages' => $sr->getSondageRunning(),

			// Actus
			'actus' => $ar->findBy(['valid' => true], ['id' => 'DESC'], 3, 0),

			// Séances
			'seances' => $seances,
			'lieu_defaut' => $slr->findOneByDefaut(true),
			'form1' => $forms_seances[0]->createView(),
			'form2' => $forms_seances[1]->createView(),

			// Calendrier
			'dateJour' => ucfirst($this->dateToFrench('now', 'l j F Y')),
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
	public function seancesForm($seances, $ser, $request, $user) 
	{
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
	 * Renvoie les 3 prochaines séances pour le formulaire de table
	 */
	public function seancesTable($ser, $user) 
	{
		$seances = $ser->getNextSeance(3);

		$result = [];
		foreach ($seances as $seance){
			$titre = ucfirst($this->dateToFrench($seance->getDate()->format('Y/m/d'), 'l d/m/Y'))." - ".$seance->getType()->getName();
			$result[$titre] = $seance->getId();
		}

		return $result;
	}

	/**
	 * Mini-tchat Ajout de contenu
	 */
	public function tchatForm($tr, $request, $user)
	{
		$tchat = new Tchat();

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
	 * Mini-tchat Ajout de contenu
	 */
	public function tableForm($tr, $seance, $seances_table, $request, $user) 
	{
		$table = new Table();

		$form = $this->createForm(TableForm::class, $table, [
			'user_id' => !empty($user) ? $user->getId() : 0,
			'seance_id' => !empty($seance) ? $seance->getId() : 0,
			'seances_table' => $seances_table,
		]);

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
}
