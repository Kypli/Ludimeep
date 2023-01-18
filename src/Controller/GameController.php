<?php

namespace App\Controller;

use App\Entity\Game;

use App\Form\GameType as GameForm;

use App\Repository\UserRepository;
use App\Repository\GameRepository;
use App\Repository\SeanceRepository;

use App\Service\Log;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/game", name="game")
 */
class GameController extends AbstractController
{
	/**
	 * @Route("/", name="")
	 */
	public function index(): Response
	{
		$form = $this->createForm(GameForm::class);

		if (!$this->isGranted('ROLE_ADMIN')){
			$form->remove('owner');
		}

		return $this->renderForm('game/index.html.twig', [
			'form' => $form,
			'user' => $this->isGranted('ROLE_USER'),
			'admin' => $this->isGranted('ROLE_ADMIN'),
		]);
	}

	/**
	 * @Route("/games", name="_games")
	 * Récupère les jeux
	 * Pour requête ajax seulement
	 */
	public function games(Request $request)
	{
		// Control request
		if (!$request->isXmlHttpRequest()){ throw new HttpException('500', 'Requête ajax uniquement'); }

		// Get entity datas
		$games = $this
			->getDoctrine()
			->getRepository(Game::class)
			->allArray()
		;

		foreach($games as $key => $game){
			$time = $game['time'];
			$games[$key]['time_hour'] = isset($time) ? $time->format('H') : null;
			$games[$key]['time_minute'] = isset($time) ? $time->format('i') : null;
			$games[$key]['owner'] = $game['nom'] == "" && $game['prenom'] == ""
				? $game['userName']
				: ucfirst($game['nom']).' '.ucfirst($game['prenom'])
			;
		}

		return new JsonResponse($games);
	}

	/**
	 * @Route("/add", name="_add", methods={"GET", "POST"}, options={"expose"=true})
	 */
	public function add(Request $request, GameRepository $gr, UserRepository $ur, Log $log): Response
	{
		// Control request
		if (!$request->isXmlHttpRequest()){ throw new HttpException('500', 'Requête ajax uniquement'); }

		$datas = $request->query->all()['datas'];

		if ($datas['name'] == ''){
			return new JsonResponse([
				'save' => false,
				'raison' => 'Nom obligatoire',
			]);
		}

		// Time
		$datas['time_hour'] = !isset($datas['time_hour']) || $datas['time_hour'] == "" ? '00' : $datas['time_hour'];
		$datas['time_minute'] = !isset($datas['time_minute']) || $datas['time_minute'] == "" ? '00' : $datas['time_minute'];
		$time = '2022-01-01 '.$datas['time_hour'].':'.$datas['time_minute'].':00';

		// Owner
		$owner = isset($datas['owner']) && !empty($datas['owner']) && $this->isGranted('ROLE_ADMIN')
			? $ur->find($datas['owner'])
			: $this->getUser()
		;

		$game = new Game();
		$game
			->setName($datas['name'])
			->setOwner($owner)
			->setNbPlayers($datas['nbPlayers'] == "" ? null : $datas['nbPlayers'])
			->setDifficult($datas['difficult'] == "" ? null : $datas['difficult'])
			->setVersion($datas['version'] == "" ? null : $datas['version'])
			->setMinAge($datas['minAge'] == "" ? null : $datas['minAge'])
			->setTime(new \DateTime($time))
		;

		$gr->add($game);
		$log->saveLog(Log::GAME, ucfirst($game->getName()));

		return new JsonResponse([
			'save' => true,
			'id' => $game->getId(),
			'user_id' => $owner->getId(),
			'userName' => $owner->getUserName(),
			'nom' => $owner->getProfil()->getNom(),
			'prenom' => $owner->getProfil()->getPrenom(),
		]);
	}

	/**
	 * @Route("/edit/{id}", name="_edit", methods={"GET", "POST"}, options={"expose"=true})
	 */
	public function edit(Request $request, Game $entity, GameRepository $gr, UserRepository $ur): Response
	{
		// Control request
		if (!$request->isXmlHttpRequest()){ throw new HttpException('500', 'Requête ajax uniquement'); }

		// Get form datas
		$datas = $request->query->get('datas');

		if (empty($entity)){
			return new JsonResponse([
				'save' => false,
				'raison' => "Aucun jeu trouvé.<br />Modification annulée.",
				'datas' => null,
			]);
		}

		if (empty($datas['name'])){
			return new JsonResponse([
				'save' => false,
				'raison' => "Le nom est obligatoire.<br />Modification annulée.",
				'datas' => null,
			]);
		}

		// Time
		$datas['time_hour'] = !isset($datas['time_hour']) || $datas['time_hour'] == "" ? '00' : $datas['time_hour'];
		$datas['time_minute'] = !isset($datas['time_minute']) || $datas['time_minute'] == "" ? '00' : $datas['time_minute'];
		$time = '2022-01-01 '.$datas['time_hour'].':'.$datas['time_minute'].':00';

		// Owner
		$owner = isset($datas['owner']) && !empty($datas['owner']) && $this->isGranted('ROLE_ADMIN')
			? $ur->find($datas['owner'])
			: $this->getUser()
		;

		// Hydrate entity
		$entity
			->setName($datas['name'])
			->setOwner($owner)
			->setNbPlayers($datas['nbPlayers'] == "" ? null : $datas['nbPlayers'])
			->setDifficult($datas['difficult'] == "" ? null : $datas['difficult'])
			->setVersion($datas['version'] == "" ? null : $datas['version'])
			->setMinAge($datas['minAge'] == "" ? null : $datas['minAge'])
			->setTime(new \DateTime($time))
		;

		// Save
		$gr->add($entity);

		return new JsonResponse([
			'save' => true,
			'user_id' => $owner->getId(),
			'userName' => $owner->getUserName(),
			'nom' => $owner->getProfil()->getNom(),
			'prenom' => $owner->getProfil()->getPrenom(),
		]);
	}

	/**
	 * @Route("/delete", name="_delete", methods={"POST"}, options={"expose"=true})
	 * Supprime un jeu
	 * Pour requête ajax seulement
	 */
	public function delete(Request $request, GameRepository $gr)
	{
		// Control request
		if (!$request->isXmlHttpRequest()){ throw new HttpException('500', 'Requête ajax uniquement'); }

		// Get form datas
		$datas = $request->query->get('datas');

		// Get Entity
		$game = $gr->find($datas['id']);

		if (empty($game)){
			return new JsonResponse([
				'save' => false,
				'raison' => "Aucun jeu trouvé.<br />Suppression annulée.",
				'datas' => null,
			]);
		}

		if (!$this->isGranted('ROLE_ADMIN') && $game->getOwner()->getId() != $this->getUser()->getId()){
			return new JsonResponse([
				'save' => false,
				'raison' => "Vous n'avez pas les droits pour supprimer ce jeu !",
				'datas' => null,
			]);
		}

		// Delete
		$gr->remove($game);

		return new JsonResponse(['save' => true]);
	}

	/**
	 * @Route("/{id}", name="_game", methods={"POST"}, options={"expose"=true})
	 * Récupère un jeu
	 * Pour requête ajax seulement
	 */
	public function game(Game $game, Request $request)
	{
		// Control request
		if (!$request->isXmlHttpRequest()){ throw new HttpException('500', 'Requête ajax uniquement'); }

		// Owner
		$owner = $game->getOwner();

		// Time
		$time = $game->getTime();
		$time_hour = isset($time) ? $time->format('H') : null;
		$time_minute = isset($time) ? $time->format('i') : null;

		return new JsonResponse([
			'id' => $game->getId(),
			'name' => $game->getName(),
			'nbPlayers' => $game->getNbPlayers(),
			'difficult' => $game->getDifficult(),
			'version' => $game->getVersion(),
			'minAge' => $game->getMinAge(),
			'time_hour' => $time_hour,
			'time_minute' => $time_minute,
			'userName' => $owner->getUserName(),
			'nom' => $owner->getProfil()->getNom(),
			'prenom' => $owner->getProfil()->getPrenom(),
			'user_id' => $owner->getId(),
		]);
	}

	/**
	 * @Route("/liste_adherant/{date}", name="_liste_adherant", options={"expose"=true})
	 * Récupère un jeu
	 * Pour requête ajax seulement
	 */
	public function listeAdherant($date, GameRepository $gr, SeanceRepository $ser, Request $request)
	{
		// Control request
		if (!$request->isXmlHttpRequest()){ throw new HttpException('500', 'Requête ajax uniquement'); }

		// Seance
		$seance = $ser->getOneSeanceByDate(str_replace('_', "/", $date));
		$seance = $seance[0];

		$liste = $gr->getListeAdherant($seance->getId(), $this->getUser()->getId());

		return new JsonResponse($liste);
	}
}
