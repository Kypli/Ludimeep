<?php

namespace App\Controller;

use App\Entity\Tchat;
use App\Entity\Table;
use App\Entity\UserAsso;
use App\Entity\UserProfil;

use App\Service\Log;
use App\Service\Discussion as DiscussionSer;
use App\Service\LigneComptable as LigneComptableSer;
use App\Service\Date as DateSer;

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

	const TCHAT_DATE_LIMIT_SHOW = '14 days';

	// Var
	private $user;

	// Service
	private $log;
	private $dateSer;
	private $discussionSer;
	private $ligneComptableSer;

	// Repository
	private $gr;
	private $ar;
	private $tar;
	private $tr;
	private $ser;
	private $sr;
	private $or;
	private $slr;
	private $car;

	public function __construct(
			Log $log,
			DateSer $dateSer,
			DiscussionSer $discussionSer,
			LigneComptableSer $ligneComptableSer,
			GameRepository $gr,
			ActuRepository $ar,
			TableRepository $tar,
			TchatRepository $tr,
			SeanceRepository $ser,
			SondageRepository $sr,
			OperationRepository $or,
			SeanceLieuRepository $slr,
			CommentActuRepository $car
	){
		$this->user = $this->user;
		$this->user_id = $this->user != null ? $this->user->getId() : 0;

		$this->log = $log;
		$this->dateSer = $dateSer;
		$this->discussionSer = $discussionSer;
		$this->ligneComptableSer = $ligneComptableSer;

		$this->gr = $gr;
		$this->tr = $tr;
		$this->sr = $sr;
		$this->ar = $ar;
		$this->or = $or;
		$this->tar = $tar;
		$this->ser = $ser;
		$this->slr = $slr;
		$this->car = $car;
	}

	/**
	 * @Route("/", name="")
	 */
	public function index(Request $request, AuthenticationUtils $authenticationUtils)
	{
		// Discussions
		$this->discussionSer->update();

		// Lignes comptables
		$this->ligneComptableSer->update();

		// Actus
		$actus = $this->ar->findBy(['valid' => true], ['id' => 'DESC'], 3, 0);

		// InteractActu
		$actus_interact = [];
		foreach ($actus as $actu){

			$actu_id = $actu->getId();
			$ca = $this->user != null ? $this->car->getCaByUserAndActu($actu_id, $user_id) : null;

			$actus_interact[$actu->getid()] = [
				'nb_aimes' => $this->car->getAimes($actu_id),
				'nb_thumb_up' => $this->car->getThumbUp($actu_id),
				'nb_thumb_down' => $this->car->getThumbDown($actu_id),
				'myAime' => $ca != null ? $ca->isAime() : false,
				'myThumb' => $ca != null ? $ca->isThumb() : null,
			];
		}

		// Séances
		$seances = $this->seances(
			$this->ser->getOldSeance(self::SEANCE_AFFICHAGE_MAX),
			$this->ser->getNextSeance(SELF::SEANCE_AFFICHAGE_MAX)
		);
		$seances_date = $this->seancesDate($seances);
		$seance_presence_forms = $this->seancesForm($seances, $request);

		// Tchat
		$tchat_form = $this->createForm(TchatForm::class);

		// Table
		$this->get('session')->set('table_nb_presence_form', 1);
		$table_form = $this->tableForm($request);
		$table_presence_forms = $this->tablesPresenceForm($seances, $request);

		// Form valid
		if ($table_form === false || $seance_presence_forms === false || $table_presence_forms === false){
			return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('home/index.html.twig',[

			// Authentification
			'error' => $authenticationUtils->getLastAuthenticationError(),		// get the login error if there is one
			'last_username' => $authenticationUtils->getLastUsername(),			// last username entered by the user

			// Solde
			'solde' => $this->user != null ? $this->or->solde($user_id) : 0,

			// Tchat
			'tchats' => $this->tr->getLastTchats(self::TCHAT_DATE_LIMIT_SHOW),
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
			'sondages' => $this->sr->getSondageRunning(),

			// Actus
			'actus' => $actus,
			'actus_interact' => $actus_interact,

			// Séances
			'seances' => $seances,
			'seances_date' => $seances_date,
			'seance_max_table' => self::SEANCE_MAX_TABLE,
			'seance_lieu_defaut' => $this->slr->findOneByDefaut(true),
			'seance_presence_form_1' => $seance_presence_forms[0]->createView(),
			'seance_presence_form_2' => $seance_presence_forms[1]->createView(),

			// Calendrier
			'dateJour' => $this->dateSer->dateToFrench('now', 'l j F Y'),
		]);
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
	public function seancesForm($seances, $request) 
	{
		/* -- Form 1 -- */
		$seance = !empty($seances) ? $seances[array_key_first($seances)] : null;
		$form1 = $this->createForm(SeancePresence1Form::class, $seance);
		$form1->handleRequest($request);

		if ($form1->isSubmitted() && $form1->isValid()){
			
			if ($this->user->inSeance($seance)){
				$seance->removePresent($this->user);

				// Retrait des tables
				$tables = $seance->getTables();
				foreach($tables as $table){
					$table->removePlayer($this->user);
					$this->tar->add($table, true);
				}

			} else {
				$seance->addPresent($this->user);
			}
			$this->ser->add($seance, true);

			return false;
		}

		/* -- Form 2 -- */
		$seance = !empty($seances) ? $seances[array_key_last($seances)] : null;

		$form2 = $this->createForm(SeancePresence2Form::class, $seance);
		$form2->handleRequest($request);

		if ($form2->isSubmitted() && $form2->isValid()){
			if ($this->user->inSeance($seance)){
				$seance->removePresent($this->user);

				// Retrait des tables
				$tables = $seance->getTables();
				foreach($tables as $table){
					$table->removePlayer($this->user);
					$this->tar->add($table, true);
				}

			} else {
				$seance->addPresent($this->user);
			}
			$this->ser->add($seance, true);

			return false;
		}

		return [$form1, $form2];
	}

	/**
	 * Renvoie les dates des prochaines séances
	 */
	public function seancesDate($seances) 
	{
		$result = [];
		foreach ($seances as $seance){
			$result[(int) $seance->getId()] = $this->dateSer->dateToFrench($seance->getDate()->format('Y/m/d'), 'l d F');
		}

		return $result;
	}

	/**
	 * Mini-tchat Ajout de contenu
	 */
	public function tableForm($request)
	{
		$table = new Table();

		$form = $this->createForm(TableForm::class, $table, [
			'user_id' => !empty($this->user) ? $this->user->getId() : 0,
			'seance_id' => 0,
			'seances_table' => [],
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid() && $this->user != null){

			$table_req = $request->request->get('table');

			// Game
			$game = null;
			if (null == $table->getGameFree()){

				if (null != $table_req['gameOwner']){
					$game = $this->gr->find($table_req['gameOwner']);

				} elseif(null != $table_req['gamePresent']){
					$game = $this->gr->find($table_req['gamePresent']);

				} elseif(null != $table_req['gameAdherant']){
					$game = $this->gr->find($table_req['gameAdherant']);

				} else {
					$this->addFlash('error', "Aucun jeu sélectionné.");
					return false;
				}
			}

			// Seance
			$seance = $this->ser->getOneSeanceByDate($table_req['date']);
			$seance = $seance[0];

			$seance->addPresent($this->user);
			$this->ser->add($seance, true);

			// Table
			$table
				->setGerant($this->user)
				->setSeance($seance)
				->setGame($game)
				->addPlayer($this->user)
			;

			$this->tar->add($table, true);

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
	public function tablesPresenceForm($seances, $request) 
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
					$this->user->isInscrit($this->user, $table)
						? $table->removePlayer($this->user)
						: $table->addPlayer($this->user) && $seance->addPresent($this->user)
					;

					$this->tar->add($table, true);
					$this->ser->add($seance, true);

					return false;
				}

				$form[] = $form_tempo;
				$ii++;
			}
		}

		return $form;
	}
}
