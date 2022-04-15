<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * @Route("/user", name="user")
 */
class UserController extends AbstractController
{
	public function __construct(UserPasswordHasherInterface $passwordHasher)
	{
		$this->passwordHasher = $passwordHasher;
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/", name="_users", methods={"GET"})
	 */
	public function liste(UserRepository $userRepository): Response
	{
		return $this->render('user/liste.html.twig', [
			'users' => $userRepository->findAll(),
		]);
	}

	/**
	 * @Route("/inscription", name="_add", methods={"GET", "POST"})
	 */
	public function add(Request $request, UserRepository $userRepository)
	{
		// Ne doit pas être membre ou être admin
		if (null !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')){
			$this->addFlash('error', 'Vous ne pouvez pas vous inscrire si vous êtes déjà membre.');
			return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
		}

		$user = new User();
		$form = $this->createForm(UserType::class, $user);
		$form
			->remove('admin')
			->remove('droitImage')
			->remove('newsletter')
			->remove('nom')
			->remove('prenom')
			->remove('mail')
			->remove('adresse')
			->remove('telephone')
			->remove('adherant')
			->remove('dateInscription')
			->remove('notoriete')
			->remove('roleCa')
			->remove('dateFinAdhesion')
			->remove('dateFinMandat')
			->remove('membreHonneur')
			->remove('commentaire')
		;
		$form->handleRequest($request);

		// Datas
		$req_user = $form->getData();

		// Valid form
		if ($form->isSubmitted() && $form->isValid() && $this->FormControl($user)){

			// Duplicate control
			if (!empty($userRepository->findByUserName($req_user->getUserName()))){
				$this->addFlash('error', "Ce login est déjà pris. Merci d'en sélectionner un autre.");

			// Save
			} else {

				// Default datas
				$user
					->setNom(strtolower($user->getNom()))
					->setPrenom(strtolower($user->getPrenom()))
					->setDroitImage(false)
					->setNewsletter(false)
					->setMembreHonneur(false)
				;

				// Encrypt password
				$user->setPassword($this->passwordHasher->hashPassword(
						$user,
						$request->request->get('user')['password'],
					))
				;

				$userRepository->add($user);
				$this->addFlash('success', 'Félicitations, vous inscription est prise en compte, vous pouvez maintenant vous connecter.');
				return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
			}
		}

		return $this->render('user/add.html.twig', [
			'user' => $user,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}", name="_show", methods={"GET"})
	 */
	public function fiche(User $user): Response
	{
		// Acces control
		if ($this->AccesControl($user->getId()) == false){
			return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('user/fiche.html.twig', [
			'user' => $user,
		]);
	}

	/**
	 * @Route("/{id}/edit", name="_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, User $user, UserRepository $userRepository): Response
	{
		// Acces control
		if ($this->AccesControl($user->getId()) == false){
			return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
		}

		$form = $this->createForm(UserType::class, $user);

		$form->remove('password');

		// Champs exclus si non-admin
		if (!$this->isGranted('ROLE_ADMIN')){
			$form
				->remove('userName')
				->remove('password')
				->remove('admin')
				->remove('adherant')
				->remove('dateInscription')
				->remove('dateFinAdhesion')
				->remove('roleCa')
				->remove('dateFinMandat')
				->remove('membreHonneur')
				->remove('commentaire')
			;
		}

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid() && $this->FormControl($user)){

			// Lower Nom + Prenom
			$user
				->setNom(strtolower($user->getNom()))
				->setPrenom(strtolower($user->getPrenom()))
			;

			// Edit admin
			if ($this->isGranted('ROLE_ADMIN')){

				// True
				$req_user = $request->request->get('user');
				if (isset($req_user['admin']) && $req_user['admin'] == 1){
					$user->setRoles(["ROLE_ADMIN"]);

				// False
				} elseif($userRepository->myFindCountAdmin() > 1){
					$user->setRoles(["ROLE_USER"]);

				// Null
				} else {
					$this->addFlash('error', 'Suppression du rôle Admin annulée, il doit au moins en rester un.');
				}
			}

			$userRepository->add($user);
			$this->addFlash('success', 'Vos modifications ont bien été prise en compte.');
			return $this->redirectToRoute('user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('user/edit.html.twig', [
			'user' => $user,
			'form' => $form,
		]);
	}

	/**
	 * @Route("/{id}", name="_delete", methods={"POST"})
	 */
	public function delete(Request $request, User $user, UserRepository $userRepository): Response
	{
		// Acces control
		if ($this->AccesControl($user->getId()) == false){
			return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
		}

		// TODO
			// Il doit rester au moins un admin

		if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
			$userRepository->remove($user);
		}

		return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
	}

	public function AccesControl($user_id)
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

	public function FormControl($user)
	{
		// Si adherant, rajouter date inscription + date fin adhesion
		if (null == $user->getAdherant() && (null == $user->getDateInscription() || null == $user->getDateFinAdhesion())){
			$this->addFlash('error', "Si l'utilisateur est un adhérant, il doit avoir une date d'inscription et de fin d'adhésion.");
			return false;
		}

		// Courriel valide
		if (!empty($user->getMail()) && !filter_var($user->getMail(), FILTER_VALIDATE_EMAIL)){
			$this->addFlash('error', "Le courriel n'est pas valide.");
			return false;
		}

		// Si newsletter, doit avoir un mail
		if (empty($user->getMail()) && $user->getNewsletter()){
			$this->addFlash('error', "Vous devez avoir un courriel pour être inscrit à la newsletter.");
			return false;
		}

		return true;
	}
}
