<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType as GameForm;
use App\Repository\GameRepository;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
		return $this->render('game/index.html.twig', [
			'user' => $this->isGranted('ROLE_USER'),
			'admin' => $this->isGranted('ROLE_ADMIN'),
			'form' => $this->createForm(GameForm::class)->createView(),
		]);
	}

	/**
	 * @Route("/games", name="_games", options={"expose"=true})
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

		return new JsonResponse($games);
	}

	/**
	 * @Route("/add", name="_add", methods={"GET", "POST"})
	 */
	public function add(Request $request, GameRepository $gameRepository): Response
	{
		$game = new Game();
		$form = $this->createForm(GameType::class, $game);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){
			$game->setOwner($this->getUser());
			$gameRepository->add($game);
			return $this->redirectToRoute('game', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('game/new.html.twig', [
			'game' => $game,
			'form' => $form,
		]);
	}

	/**
	 * @Route("/{id}", name="_show", methods={"GET"})
	 */
	public function show(Game $game): Response
	{
		return $this->render('game/show.html.twig', [
			'game' => $game,
		]);
	}

	/**
	 * @Route("/edit/{id}", name="_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, Game $game, GameRepository $gameRepository): Response
	{
		$form = $this->createForm(GameType::class, $game);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$gameRepository->add($game);
			return $this->redirectToRoute('game', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('game/edit.html.twig', [
			'game' => $game,
			'form' => $form,
		]);
	}

	/**
	 * @Route("/{id}", name="_delete", methods={"POST"})
	 */
	public function delete(Request $request, Game $game, GameRepository $gameRepository): Response
	{
		if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
			$gameRepository->remove($game);
		}

		return $this->redirectToRoute('game', [], Response::HTTP_SEE_OTHER);
	}

	/**
	 * @Route("/{id}", name="_game", methods={"POST"})
	 * Récupère un jeu
	 * Pour requête ajax seulement
	 */
	public function game(Game $game, Request $request)
	{
		// Control request
		if (!$request->isXmlHttpRequest()){ throw new HttpException('500', 'Requête ajax uniquement'); }

		return new JsonResponse([
			'id' => $game->getId(),
			'name' => $game->getName(),
			'nbPlayers' => $game->getNbPlayers(),
			'difficult' => $game->getDifficult(),
			'version' => $game->getVersion(),
			'minAge' => $game->getMinAge(),
			'time' => $game->getTime(),
		]);
	}
}
