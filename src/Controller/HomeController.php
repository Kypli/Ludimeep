<?php

namespace App\Controller;

use App\Entity\Tchat;
use App\Entity\Table;
use App\Entity\UserAsso;
use App\Entity\UserProfil;

use App\Service\Log;
use App\Service\Discussion as DiscussionSer;
use App\Service\LigneComptable as LigneComptableSer;

use App\Repository\UserRepository;
use App\Repository\UserProfilRepository;
use App\Repository\UserAssoRepository;

use App\Repository\ActuRepository;
use App\Repository\GameRepository;
use App\Repository\TableRepository;
use App\Repository\TchatRepository;
use App\Repository\SeanceRepository;
use App\Repository\SondageRepository;
use App\Repository\OperationRepository;
use App\Repository\SeanceLieuRepository;
use App\Repository\CommentActuRepository;

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

	const TABLE_PLAYERS_MAX = 12;

	private $log;

	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	/**
	 * @Route("/", name="")
	 */
	public function index(
		Request $request,
		ActuRepository $ar,
		GameRepository $gr,
		TableRepository $tar,
		TchatRepository $tr,
		SeanceRepository $ser,
		SondageRepository $sr,
		OperationRepository $or,
		SeanceLieuRepository $slr,
		CommentActuRepository $car,
		DiscussionSer $discussionSer,
		LigneComptableSer $ligneComptableSer,
		AuthenticationUtils $authenticationUtils
	){
		// User
		$user = $this->getUser();
		$user_id = $user != null ? $user->getId() : 0;

		// Discussions
		$discussionSer->update();

		// Lignes comptables
		$ligneComptableSer->update();

		// Actus
		$actus = $ar->findBy(['valid' => true], ['id' => 'DESC'], 3, 0);

		// InteractActu
		$actus_interact = [];
		foreach ($actus as $actu){

			$actu_id = $actu->getId();
			$ca = $user != null ? $car->getCaByUserAndActu($actu_id, $user_id) : null;

			$actus_interact[$actu->getid()] = [
				'nb_aimes' => $car->getAimes($actu_id),
				'nb_thumb_up' => $car->getThumbUp($actu_id),
				'nb_thumb_down' => $car->getThumbDown($actu_id),
				'myAime' => $ca != null ? $ca->isAime() : false,
				'myThumb' => $ca != null ? $ca->isThumb() : null,
			];
		}

		// Séances
		$seances_table = $this->seancesTable($ser, $user);
		$seances = $this->seances($ser->getOldSeance(self::SEANCE_AFFICHAGE_MAX), $ser->getNextSeance(SELF::SEANCE_AFFICHAGE_MAX));
		$seance_presence_forms = $this->seancesForm($seances, $ser, $tar, $request, $user);

		// Tchat
		$tchat_form = $this->tchatForm($tr, $request, $user);

		// Table
		$this->get('session')->set('table_nb_presence_form', 1);
		$table_form = $this->tableForm($tar, $ser, $gr, $request, $user);
		$table_presence_forms = $this->tablesPresenceForm($seances, $tar, $ser, $request, $user);

		// Form valid
		if (
			$seance_presence_forms === false ||
			$table_presence_forms === false ||
			$tchat_form === false ||
			$table_form === false
		){ return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER); }

		return $this->render('home/index.html.twig',[

			// Authentification
			'error' => $authenticationUtils->getLastAuthenticationError(),		// get the login error if there is one
			'last_username' => $authenticationUtils->getLastUsername(),			// last username entered by the user

			// Solde
			'solde' => $user != null ? $or->solde($user_id) : 0,

			// Tchat
			'tchats' => $tr->getLastTchats(),
			'tchat_form' => $tchat_form->createView(),

			// Tables
			'table_form' => $table_form->createView(),
			'table_players_max' => self::TABLE_PLAYERS_MAX,
			'table_presence_form_1' => isset($table_presence_forms[0]) ? $table_presence_forms[0]->createView() : null,
			'table_presence_form_2' => isset($table_presence_forms[1]) ? $table_presence_forms[1]->createView() : null,
			'table_presence_form_3' => isset($table_presence_forms[2]) ? $table_presence_forms[2]->createView() : null,
			'table_presence_form_4' => isset($table_presence_forms[3]) ? $table_presence_forms[3]->createView() : null,
			'table_presence_form_5' => isset($table_presence_forms[4]) ? $table_presence_forms[4]->createView() : null,
			'table_presence_form_6' => isset($table_presence_forms[5]) ? $table_presence_forms[5]->createView() : null,
			'table_presence_form_7' => isset($table_presence_forms[6]) ? $table_presence_forms[6]->createView() : null,
			'table_presence_form_8' => isset($table_presence_forms[7]) ? $table_presence_forms[7]->createView() : null,

			// Sondage
			'request' => $request,
			'sondages' => $sr->getSondageRunning(),

			// Actus
			'actus' => $actus,
			'actus_interact' => $actus_interact,

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
	public function tableForm($tar, $ser, $gr, $request, $user)
	{
		$table = new Table();

		$form = $this->createForm(TableForm::class, $table, [
			'user_id' => !empty($user) ? $user->getId() : 0,
			'seance_id' => 0,
			'seances_table' => [],
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid() && $user != null){

			$table_req = $request->request->get('table');

			// Game
			$game = null;
			if (null == $table->getGameFree()){

				if (null != $table_req['gameOwner']){
					$game = $gr->find($table_req['gameOwner']);

				} elseif(null != $table_req['gamePresent']){
					$game = $gr->find($table_req['gamePresent']);

				} elseif(null != $table_req['gameAdherant']){
					$game = $gr->find($table_req['gameAdherant']);

				} else {
					$this->addFlash('error', "Aucun jeu sélectionné.");
					return false;
				}
			}

			// Seance
			$seance = $ser->getOneSeanceByDate($table_req['date']);
			$seance = $seance[0];

			$seance->addPresent($user);
			$ser->add($seance, true);

			// Table
			$table
				->setGerant($user)
				->setSeance($seance)
				->setGame($game)
				->addPlayer($user)
			;

			$tar->add($table, true);

			// Log
			$game_name = empty($table->getGameFree())
				? $table->getGame()->getName()
				: $table->getGameFree()
			;
			$this->log->saveLog(Log::TABLE, ucfirst($game_name));

			return false;
		}

		return $form;
	}

	/**
	 * Gère les formulaires d'inscription aux tables
	 */
	public function tablesPresenceForm($seances, $tar, $ser, $request, $user) 
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
				$ii++;
			}
		}

		return $form;
	}
}
