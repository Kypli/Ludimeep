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
use App\Form\SeancePresence1Type as SeancePresence1Form;
use App\Form\SeancePresence2Type as SeancePresence2Form;
use App\Form\tablePresence\TablePresence1Type as TablePresence1Form;
use App\Form\tablePresence\TablePresence2Type as TablePresence2Form;
use App\Form\tablePresence\TablePresence3Type as TablePresence3Form;
use App\Form\tablePresence\TablePresence4Type as TablePresence4Form;
use App\Form\tablePresence\TablePresence5Type as TablePresence5Form;
use App\Form\tablePresence\TablePresence6Type as TablePresence6Form;
use App\Form\tablePresence\TablePresence7Type as TablePresence7Form;
use App\Form\tablePresence\TablePresence8Type as TablePresence8Form;

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
	const SEANCE_AFFICHAGE_MAX = 2;
	const SEANCE_MAX_TABLE = 2;

	const TABLE_MAX_PLAYERS = 12;

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
		$seances_table = $this->seancesTable($ser, $user);
		$seances = $this->seances($ser->getOldSeance(self::SEANCE_AFFICHAGE_MAX), $ser->getNextSeance(SELF::SEANCE_AFFICHAGE_MAX));
		$seance_presence_forms = $this->seancesForm($seances, $ser, $tar, $request, $user);

		// Tchat
		$tchat_form = $this->tchatForm($tr, $request, $user);

		// Table
		$table_form = $this->tableForm($tar, $seances[0], $seances_table, $request, $user);
		$table_player_forms = $this->tablesForm($seances, $tar, $ser, $request, $user);

		// Form valid
		if (
			$seance_presence_forms === false ||
			$table_player_forms === false ||
			$tchat_form === false ||
			$table_form === false
		){ return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER); }

		return $this->render('home/index.html.twig',[

			// Authentification
			'error' => $authenticationUtils->getLastAuthenticationError(),		// get the login error if there is one
			'last_username' => $authenticationUtils->getLastUsername(),			// last username entered by the user

			// Tchat
			'tchats' => $tr->getLastTchats(),
			'tchat_form' => $tchat_form->createView(),

			// Tables
			'table_form' => $table_form->createView(),
			'table_max_players' => self::TABLE_MAX_PLAYERS,
			'table_player_form_1' => isset($table_player_forms[0]) ? $table_player_forms[0]->createView() : null,
			'table_player_form_2' => isset($table_player_forms[1]) ? $table_player_forms[1]->createView() : null,
			'table_player_form_3' => isset($table_player_forms[2]) ? $table_player_forms[2]->createView() : null,
			'table_player_form_4' => isset($table_player_forms[3]) ? $table_player_forms[3]->createView() : null,
			'table_player_form_5' => isset($table_player_forms[4]) ? $table_player_forms[4]->createView() : null,
			'table_player_form_6' => isset($table_player_forms[5]) ? $table_player_forms[5]->createView() : null,
			'table_player_form_7' => isset($table_player_forms[6]) ? $table_player_forms[6]->createView() : null,
			'table_player_form_8' => isset($table_player_forms[7]) ? $table_player_forms[7]->createView() : null,

			// Sondage
			'request' => $request,
			'sondages' => $sr->getSondageRunning(),

			// Actus
			'actus' => $ar->findBy(['valid' => true], ['id' => 'DESC'], 3, 0),

			// Séances
			'seances' => $seances,
			'seance_max_table' => self::SEANCE_MAX_TABLE,
			'seance_lieu_defaut' => $slr->findOneByDefaut(true),
			'seance_presence_form_1' => $seance_presence_forms[0]->createView(),
			'seance_presence_form_2' => $seance_presence_forms[1]->createView(),

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
	public function seancesForm($seances, $ser, $tar, $request, $user) 
	{
		/* -- Form 1 -- */
		$seance = !empty($seances) ? $seances[array_key_first($seances)] : null;
		$form1 = $this->createForm(SeancePresence1Form::class, $seance);
		$form1->handleRequest($request);

		if ($form1->isSubmitted() && $form1->isValid()){
			
			if ($user->inSeance($seance)){
				$seance->removePresent($user);

				// Retrait des tables
				$tables = $seance->getTables();
				foreach($tables as $table){
					$table->removePlayer($user);
					$tar->add($table, true);
				}

			} else {
				$seance->addPresent($user);
			}
			$ser->add($seance, true);

			return false;
		}

		/* -- Form 2 -- */
		$seance = !empty($seances) ? $seances[array_key_last($seances)] : null;

		$form2 = $this->createForm(SeancePresence2Form::class, $seance);
		$form2->handleRequest($request);

		if ($form2->isSubmitted() && $form2->isValid()){
			$user->inSeance($seance)
				? $seance->removePresent($user)
				: $seance->addPresent($user)
			;
			$ser->add($seance, true);
			$tar->add($table, true);

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

	/**
	 * Gère les formulaires d'inscription aux tables
	 */
	public function tablesForm($seances, $tar, $ser, $request, $user) 
	{
		$ii = 1;
		$form = [];

		for ($i = 0; $i < self::SEANCE_AFFICHAGE_MAX; $i++){

			$seance = $seances[$i];
			$tables = $seance->getTables();

			foreach($tables as $key => $table){

				switch ($ii){
					case 1:
						$form_tempo = $this->createForm(TablePresence1Form::class, $table);
						break;
					case 2:
						$form_tempo = $this->createForm(TablePresence2Form::class, $table);
						break;
					case 3:
						$form_tempo = $this->createForm(TablePresence3Form::class, $table);
						break;
					case 4:
						$form_tempo = $this->createForm(TablePresence4Form::class, $table);
						break;
					case 5:
						$form_tempo = $this->createForm(TablePresence5Form::class, $table);
						break;
					case 6:
						$form_tempo = $this->createForm(TablePresence6Form::class, $table);
						break;
					case 7:
						$form_tempo = $this->createForm(TablePresence7Form::class, $table);
						break;
					case 8:
						$form_tempo = $this->createForm(TablePresence8Form::class, $table);
						break;
					
					default:
						$form_tempo = $this->createForm(TablePresence1Form::class, $table);
						break;
				}
				$form_tempo->handleRequest($request);

				if ($form_tempo->isSubmitted() && $form_tempo->isValid()){
					$user->isInscrit($user, $table)
						? $table->removePlayer($user)
						: $table->addPlayer($user) && $seance->addPresent($user)
					;

					$tar->add($table, true);
					$ser->add($seance, true);

					return false;
				}

				$form[] = $form_tempo;
			}
		}

		return $form;
	}
}
