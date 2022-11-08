<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Operation;
use App\Form\OperationType;
use App\Repository\OperationRepository;
use App\Repository\UserRepository;

use App\Service\LigneComptable as LigneComptableSer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/operation", name="operation")
 */
class OperationController extends AbstractController
{
	/**
	 * @Route("/add/{user}", name="_add", defaults={"user": null}, methods={"GET", "POST"})
	 */
	public function add(Request $request, $user, OperationRepository $or): Response
	{
		if (null != $user){
			$ur = $this->getDoctrine()->getRepository(User::class);
			$user = $ur->find($user);
		}

		$operation = new Operation();
		$form = $this->createForm(OperationType::class, $operation, ['user' => $user]);

		if (!$this->isGranted('ROLE_ADMIN')){
			$form
				->remove('valid')
				->remove('user')
			;
			$operation->setUser($this->getUser());
		}

		$form->handleRequest($request);

		// Form
		if ($form->isSubmitted()){

			// Control
			if (!$this->isGranted('ROLE_ADMIN') && $operation->isValid() == true){
				$this->addFlash('error', "Les opérations validées ne peuvent être modifiées que par un admin.");
				return $this->redirectToRoute('operation_add', [], Response::HTTP_SEE_OTHER);

			} elseif ((float)$operation->getNumber() == 0.0 || (float)$operation->getNumber() == 0){
				$this->addFlash('error', "Le numéro doit être un chiffre et être différent de 0");
				return $this->redirectToRoute('operation_add', [], Response::HTTP_SEE_OTHER);
			}
			
			if ($form->isValid()){
				$or->add($operation, true);
			}

			return $this->redirectToRoute('operation_show', ['id' => $operation->getUser()->getId()], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('operation/add.html.twig', [
			'form' => $form,
			'type' => 'add',
			'operation' => $operation,
		]);
	}

	/**
	 * @Route("/{id}", name="_show", methods={"GET"})
	 */
	public function show(Request $request, User $user, OperationRepository $or, LigneComptableSer $ligneComptableSer): Response
	{
		// Acces control
		if ($this->accesControl($user->getId()) == false){
			return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
		}

		// Lignes comptables
		$ligneComptableSer->update();

		return $this->render('operation/show.html.twig', [
			'user' => $user,
			'solde' => $or->solde($user->getId()),
			'operations' => $or->findBy(['user' => $user], ['date' => 'DESC'], null, 0),
		]);
	}

	/**
	 * @Route("/edit/{id}", name="_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, Operation $operation, OperationRepository $or): Response
	{
		// Acces control
		if ($this->accesControl($operation->getUser()->getId()) == false){
			return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
		}

		$form = $this->createForm(OperationType::class, $operation);

		$form->remove('user');
		if (!$this->isGranted('ROLE_ADMIN')){ $form->remove('valid'); }

		$form->handleRequest($request);

		// Form
		if ($form->isSubmitted()){

			// Control
			if (!$this->isGranted('ROLE_ADMIN') && $operation->isValid() == true){
				$this->addFlash('error', "Les opérations validées ne peuvent être modifiées que par un admin.");
				return $this->redirectToRoute('operation_add', [], Response::HTTP_SEE_OTHER);

			} elseif ((float)$operation->getNumber() == 0.0 || (float)$operation->getNumber() == 0){
				$this->addFlash('error', "Le numéro doit être un chiffre et être différent de 0");
				return $this->redirectToRoute('operation_add', [], Response::HTTP_SEE_OTHER);
			}
			
			if ($form->isValid()){
				$or->add($operation, true);
			}

			return $this->redirectToRoute('operation_show', ['id' => $operation->getUser()->getId()], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('operation/add.html.twig', [
			'form' => $form,
			'type' => 'edit',
			'operation' => $operation,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/valid/{id}", name="_valid", methods={"GET"})
	 * Valide  une ligne non validées d'un user
	 */
	public function valid(OperationRepository $or, Operation $operation): Response
	{
		$operation->setValid(true);
		$or->add($operation, true);

		return $this->redirectToRoute('operation_show', ['id' => $operation->getUser()->getId()], Response::HTTP_SEE_OTHER);
	}

	/**
	 * @Route("/delete/{id}", name="_delete", methods={"POST"})
	 */
	public function delete(Request $request, Operation $operation, OperationRepository $or): Response
	{
		if ($this->isCsrfTokenValid('delete'.$operation->getId(), $request->request->get('_token'))){
			if (!$this->isGranted('ROLE_ADMIN') && $operation->isValid() == true){
				$this->addFlash('error', "Les opérations validées ne peuvent être supprimés que par un admin.");

			} else {
				$or->remove($operation, true);
			}
		}

		return $this->redirectToRoute('operation_show', ['id' => $operation->getUser()->getId()], Response::HTTP_SEE_OTHER);
	}

	// Control l'accès
	public function accesControl($user_id)
	{
		// Si non-admin
		if (!$this->isGranted('ROLE_ADMIN')){

			// Doit être connecté
			if (null === $this->getUser()){
				$this->addFlash('error', 'Vous devez être connecté pour accéder à votre profil.');
				return false;
			}

			// Doit être propriétaire
			if ($user_id != $this->getUser()->getId()){
				$this->addFlash('error', 'Vous devez être propriétaire de ce profil.');
				return false;
			}
		}
		return true;
	}

	/**
	 * @Route("/unvalid/{id}", name="_vote")
	 * Renvoie le nombre de lignes non validées d'un user
	 */
	public function unvalid(User $user, OperationRepository $or): Response
	{
		return new JsonResponse((int) $or->encoursByUser($user->getId()));
	}
}
