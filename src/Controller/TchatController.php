<?php

namespace App\Controller;

use App\Entity\Tchat;

use App\Repository\TchatRepository;

use App\Service\Log;
use App\Service\FileUploader;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/tchat", name="tchat")
 */
class TchatController extends AbstractController
{
	/**
	 * @Route("/new/", name="_new", methods={"GET", "POST"})
	 * Mini-tchat Enregistre un nouveau message
	 * Pour requête ajax seulement
	 */
	public function new(TchatRepository $tr, Request $request)
	{
		// Control request
		if (!$request->isXmlHttpRequest()){ throw new HttpException('500', 'Requête ajax uniquement'); }

		$user = $this->getUser();
		$content = $request->query->all()['content'];

		if (null != $user && $content != null && $content != ''){

			$tchat = new Tchat();
			$tchat
				->setDate(new \Datetime('now'))
				->setUser($user)
				->setContent($content)
			;

			$tr->add($tchat, true);
		}

		return new JsonResponse([
			'save' => true,
			'id' => isset($tchat) ? $tchat->getId() : null,
		]);
	}
	/**
	 * @Route("/add/{id}", name="_add", methods={"GET", "POST"})
	 * Mini-tchat Récupère html d'un message
	 * Pour requête ajax seulement
	 */
	public function add(Tchat $tchat, Request $request)
	{
		// Control request
		if (!$request->isXmlHttpRequest()){ throw new HttpException('500', 'Requête ajax uniquement'); }

		$user = $this->getUser();
		$render = $this->render('tchat/_message.html.twig', [
			't' => [
				"login" => $user->getUserName(),
				"nom" => $user->getProfil()->getNom(),
				"prenom" => $user->getProfil()->getPrenom(),
				"content" => $tchat->getContent(),
  				"date" => $tchat->getDate(),
			],
		])->getContent();

		return new JsonResponse([
			'save' => true,
			'render' => $render,
		]);
	}
}



