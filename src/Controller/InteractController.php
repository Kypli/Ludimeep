<?php

namespace App\Controller;

use App\Entity\Actu;
use App\Entity\CommentActu;

use App\Repository\ActuRepository;
use App\Repository\CommentActuRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/interact", name="interact")
 */
class InteractController extends AbstractController
{
	/**
	 * @Route("/actu/{id}", name="_actu", methods={"GET", "POST"})
	 * Modifie les interactions des actus
	 * Pour requête ajax seulement
	 */
	public function actu(Actu $actu, CommentActuRepository $car, Request $request): Response
	{
		// Control request
		if (!$request->isXmlHttpRequest()){ throw new HttpException('500', 'Requête ajax uniquement'); }

		$user = $this->getUser();
		$datas = $request->query->all()['action'];

		$ca = $car->getCaByUserAndActu($actu->getId(), $user->getId());

		if ($ca){

			if ($datas == 'heart'){
				$ca->setAime(!$ca->isAime());

			} elseif ($datas == 'thumbUp'){
				$ca->setThumb($ca->isThumb() === true ? null : true);

			} else {
				$ca->setThumb($ca->isThumb() === false ? null : false);
			}

		} else {
			$ca = new CommentActu();
			$ca->setUser($user)->setActu($actu);

			$datas == 'heart'
				? $ca->setAime(true) 
				: $ca->setThumb(true)
			;
		}

		$car->add($ca, true);

		return new Response(true);
	}
}
