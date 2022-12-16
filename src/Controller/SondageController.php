<?php

namespace App\Controller;

use App\Entity\Sondage;
use App\Entity\SondageUser;

use App\Repository\SondageRepository;
use App\Repository\SondageUserRepository;

use App\Form\SondageType;
use App\Form\SondageVote1Type;
use App\Form\SondageVote2Type;

use App\Service\Log;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/sondage", name="sondage")
 */
class SondageController extends AbstractController
{
	/**
	 * @Route("/", name="", methods={"GET"})
	 */
	public function index(SondageRepository $sr): Response
	{
		return $this->render('sondage/index.html.twig', [
			'sondages' => $sr->findAll(),
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/add", name="_add", methods={"GET", "POST"})
	 */
	public function add(Request $request, SondageRepository $sr, Log $log): Response
	{
		$sondage = new Sondage();
		$form = $this->createForm(SondageType::class, $sondage);
		$form->handleRequest($request);
		$sondage = $form->getData();

		if ($form->isSubmitted() && $form->isValid()){

			$control = $this->controlForm($sondage, $sr);

			$control === true
				? $log->saveLog(Log::SONDAGE, ucfirst($sondage->getTitle())) && $sr->add($sondage, true)
				: $this->addFlash('error', $control)
			;

			return $this->redirectToRoute('sondage', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('sondage/add.html.twig', [
			'form' => $form,
			'sondage' => $sondage,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/{id}/edit", name="_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, Sondage $sondage, SondageRepository $sr): Response
	{
		$form = $this->createForm(SondageType::class, $sondage);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){

			$control = $this->controlForm($sondage, $sr);

			$control !== true
				? $this->addFlash('error', $control)
				: $sr->add($sondage, true)
			;

			return $this->redirectToRoute('sondage', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('sondage/edit.html.twig', [
			'form' => $form,
			'sondage' => $sondage,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/{id}", name="_delete", methods={"POST"})
	 */
	public function delete(Request $request, Sondage $sondage, SondageRepository $sr, SondageUserRepository $sur): Response
	{
		if ($this->isCsrfTokenValid('delete'.$sondage->getId(), $request->request->get('_token'))){
			
			$votants = $sondage->getVotants();

			foreach($votants as $votant){
				$sur->remove($votant, true);
			}

			$sr->remove($sondage, true);
		}

		return $this->redirectToRoute('sondage', [], Response::HTTP_SEE_OTHER);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function controlForm(Sondage $sondage, SondageRepository $sr)
	{
		// Start < End
		if ($sondage->getStart() >= $sondage->getEnd()){
			return "La date de départ doit être inférieur à la date de fin.";
		}

		// Pas de doublons
		$lines = [];
		for ($i=1; $i <= 8; $i++){
			$line = 'getLine'.$i;
			$line = $sondage->$line();

			if ($line != null){

				if (in_array($line, $lines)){
					return "Une ligne ne peut être identique à une autre.";
				} else {
					$lines[] = $line;
				}
			}
		}

		return true;
	}

	/**
	 * @Route("/result/{id}", name="_result")
	 * Envoie les datas d'un sondage
	 */
	public function result(Sondage $sondage, SondageRepository $sr, Request $request): Response
	{
		// Control request
		if (!$request->isXmlHttpRequest()){ throw new HttpException('500', 'Requête ajax uniquement'); }

		// Couleur ['Rouge', 'Bleu', 'Yellow', 'Turquoise', 'Purple', 'Orange', 'Vert', 'Rose'];
		$datas = [];
		$labels = [];

		$votants = $sondage->getVotants();

		// Get labels and initialise Results
		for($i = 1; $i <= 8; $i++){

			$label = 'getLine'.$i;

			if (!empty($sondage->$label())){
				$results[$i] = 0;
				$labels[] = $sondage->$label();
			}
		}

		// Get Results
		foreach($votants as $votant){
			$results[$votant->getVote()]++;
		}

		// Convert Result to datas
		foreach($results as $data){
			$datas[] = $data;
		}

		return new JsonResponse([
			'datas' => $datas,
			'labels' => $labels,
		]);
	}

	/**
	 * @Route("/getVotantsBySondageId/{id}", name="_getVotantsBySondageId")
	 * Retoune le nombre de votants pour un sondage
	 */
	public function getVotantsBySondageId(Sondage $sondage, SondageUserRepository $sur)
	{
		if (empty($sondage)){
			return new Response(0);
		}

		return new Response($sur->getVotantsBySondageId($sondage->getId()));
	}

	/**
	 * @Route("/vote/{id}", name="_vote")
	 * Retoune le formulaire d'un sondage ou le résultat si le user à déja voté
	 */
	public function vote(Sondage $sondage, Request $request, $form_number, $odd, SondageUserRepository $sur)
	{
		// Sondage questions
		$sondage_questions = [];
		for ($i=1; $i <= 8; $i++){

			$line = 'getLine'.$i;
			if (null !== $sondage->$line()){
				$sondage_questions[$sondage->$line()] = $i;
			}
		}

		$form = $form_number == '1'
			? $this->createForm(SondageVote1Type::class, [], ['lines' => $sondage_questions])
			: $this->createForm(SondageVote2Type::class, [], ['lines' => $sondage_questions])
		;

		$form->handleRequest($request);
		$sondage_datas = $form->getData();
		$user = $this->getUser();

		if ($form->isSubmitted() && $form->isValid() && !$sondage->voted($user->getId())){

			$vote_result = $sondage_datas['vote'];

			$vote = new SondageUser();

			$vote
				->setSondage($sondage)
				->setVotant($user)
				->setVote($vote_result)
				->setDate(new \Datetime('now'))
			;

			$sur->add($vote, true);

			return $this->render('sondage/_sondage.html.twig', [
				's' => $sondage,
				'vote' => $vote_result,
			]);
		}

		return $this->renderForm('sondage/_vote.html.twig', [
			's' => $sondage,
			'form' => $form,
			'odd' => $odd,
		]);
	}

	/**
	 * @Route("/my_vote/{id}", name="_my_vote")
	 * Renvoie le vote d'un
	 */
	public function myVote(Sondage $sondage): Response
	{
		$user = $this->getUSer();

		if (null == $user){
			return new JsonResponse(0);
		}

		return new JsonResponse((int) $this->getDoctrine()->getRepository(SondageUser::class)->myVote($sondage->getId(), $user->getId()));
	}
}
