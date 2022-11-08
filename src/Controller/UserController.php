<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserAsso;
use App\Entity\UserProfil;

use App\Repository\UserRepository;
use App\Repository\UserAssoRepository;
use App\Repository\UserProfilRepository;
use App\Repository\DiscussionRepository;

use App\Form\UserType;

use App\Service\LigneComptable as LigneComptableSer;

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
	 * @Route("/", name="", methods={"GET"})
	 */
	public function index(UserRepository $ur, LigneComptableSer $ligneComptableSer): Response
	{
		// Lignes comptables
		$ligneComptableSer->update();

		return $this->render('user/index.html.twig', [
			'users' => $ur->findAll(),
		]);
	}

	/**
	 * @Route("/inscription", name="_add", methods={"GET", "POST"})
	 */
	public function add(Request $request, UserRepository $ur, UserProfilRepository $upr, UserAssoRepository $uar)
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
			->remove('anonyme')
			->remove('ip')
			->remove('accesPhoto')
			->remove('accesPhotoLanceurAlerte')
			->remove('newsletter')
			->remove('commentaire')
			->remove('profil')
			->remove('asso')
		;

		$form->handleRequest($request);

		// Valid form
		if ($form->isSubmitted() && $form->isValid()){

			// Duplicate control
			if (!empty($ur->findByUserName($form->getData()->getUserName()))){
				$this->addFlash('error', "Ce login est déjà pris. Merci d'en sélectionner un autre.");

			// Save
			} else {

				// Default datas
				$user
					->setNewsletter(false)
					->setRoles(["ROLE_USER"])
					->setPassword($this->passwordHasher->hashPassword(
						$user,
						$request->request->get('user')['password'],
					))
				;

				$userProfil = new UserProfil();
				$userProfil
					->setUser($user)
				;

				$userAsso = new UserAsso();
				$userAsso
					->setDroitImage(false)
					->setMembreHonneur(false)
					->setUser($user)
				;

				$ur->add($user);
				$upr->add($userProfil);
				$uar->add($userAsso);

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
	 * Ajoute un utilisateur anonyme
	 */
	public function addAnonyme($mail, $ip)
	{
		$user = new User();
		$ur = $this->getDoctrine()->getRepository(User::class);
		$upr = $this->getDoctrine()->getRepository(UserProfil::class);
		$uar = $this->getDoctrine()->getRepository(UserAsso::class);

		// Nombre anonyme
		$count = (int) $ur->countAnonymous();
		$count++;

		// Login + mdp
		$login = 'Visiteur'.$count;
		$mdp = $this->randMdp();

		// User datas
		$user
			->setUserName($login)
			->setPassword($this->passwordHasher->hashPassword(
				$user,
				$mdp,
			))
			->setRoles(["ROLE_USER"])
			->setAnonyme(true)
			->setIp($ip)
			->setPasswordTempo($mdp)

		;

		$userProfil = new UserProfil();
		$userProfil
			->setMail($mail)
			->setUser($user)
		;

		$userAsso = new UserAsso();
		$userAsso
			->setUser($user)
		;

		$ur->add($user);
		$upr->add($userProfil);
		$uar->add($userAsso);

		return [
			'user' => $user,
			'login' => $login,
			'mdp' => $mdp,
		];
	}

	/**
	 * @Route("/{id}", name="_show", methods={"GET"})
	 */
	public function show(User $user): Response
	{
		// Acces control
		if ($this->accesControl($user->getId()) == false){
			return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('user/show.html.twig', [
			'user' => $user,
		]);
	}

	/**
	 * @Route("/edit/{id}", name="_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, User $user, UserRepository $userRepository): Response
	{
		// Acces control
		if ($this->accesControl($user->getId()) == false){
			return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
		}

		$form = $this->createForm(UserType::class, $user);
		$req_user = $request->request->get('user');

		// Champs exclusif à l'admin
		if (!$this->isGranted('ROLE_ADMIN')){
			$form
				->remove('userName')
				->remove('admin')
				->remove('anonyme')
				->remove('ip')
				->remove('accesPhoto')
				->remove('accesPhotoLanceurAlerte')
				->remove('adherant')
				->remove('dateInscription')
				->remove('dateFinAdhesion')
				->remove('roleCa')
				->remove('dateFinMandat')
				->remove('membreHonneur')
				->remove('commentaire')
			;
		}

		// Alimenter dans le request le champ password si inutilisé
		if (null !== $request->request->get('user') && $request->request->get('user')['password'] == ''){
			$noEditPassword = true;
			$requestArray = $request->request->all();
			$requestArray['user']['password'] = $form->getData()->getPassword();
			$request->request->replace($requestArray);
		}

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid() && $this->formControl($user)){

			// Lower Nom + Prenom
			$user->getProfil()
				->setNom(strtolower($user->getProfil()->getNom()))
				->setPrenom(strtolower($user->getProfil()->getPrenom()))
			;

			// Edit admin
			if ($this->isGranted('ROLE_ADMIN')){

				// True
				if (isset($req_user['admin']) && $req_user['admin'] == 1){
					$user->setRoles(["ROLE_ADMIN"]);

				// False
				} elseif(!$user->isAdmin() || ($user->isAdmin() && $userRepository->countAdmin() > 1)){
					$user->setRoles(["ROLE_USER"]);

				// Null
				} else {
					$this->addFlash('error', 'Suppression du rôle Admin annulée, il doit au moins en rester un.');
				}
			}

			// Si plus anonyme, retrait du mdp temporaire + ip
			if (!$user->getAnonyme()){
				$user
					->setPasswordTempo('')
					->setIp('')
				;
			}

			// Encrypt password
			if (!isset($noEditPassword)){
				$user->setPassword($this->passwordHasher->hashPassword(
						$user,
						$form->getData()->getPassword(),
					))
				;
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
	 * @Route("/delete/{id}", name="_delete", methods={"POST"})
	 */
	public function delete(Request $request, User $user, UserRepository $ur, DiscussionRepository $dr): Response
	{
		// Acces control
		if ($this->accesControl($user->getId()) == false){
			return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
		}

		// Doit rester 1 admin
		$countAdmin = $ur->countAdmin();
		if (
			$countAdmin > 1 ||
			(
				$countAdmin == 1 &&
				!in_array($user->getId(), $ur->getAdminsId())
			)
		){
			if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))){

				// Delete messages
				foreach ($user->getDiscussionsAuteur() as $discussion){
					$dr->remove($discussion);
				}
				foreach ($user->getDiscussionsDestinataire() as $discussion){
					$dr->remove($discussion);
				}

				// Delete
				$ur->remove($user);
			}	

		} else {
			$this->addFlash('error', 'Il doit rester au moins 1 admin.');
		}

		return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
	}

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

	public function formControl($user)
	{
		// Si adhérant, rajouter date inscription + date fin adhesion
		if (
			null != $user->getAsso()->isAdherant() &&
			(
				null == $user->getAsso()->getDateInscription() ||
				null == $user->getAsso()->getDateFinAdhesion()
			)
		){
			$this->addFlash('error', "Si l'utilisateur est un adhérant, il doit avoir une date d'inscription et de fin d'adhésion.");
			return false;
		}

		// Courriel valide
		if (!empty($user->getProfil()->getMail()) && !filter_var($user->getProfil()->getMail(), FILTER_VALIDATE_EMAIL)){
			$this->addFlash('error', "Le courriel n'est pas valide.");
			return false;
		}

		// Si newsletter, doit avoir un mail
		if (empty($user->getProfil()->getMail()) && $user->isNewsletter()){
			$this->addFlash('error', "Vous devez avoir un courriel pour être inscrit à la newsletter.");
			return false;
		}

		return true;
	}

	public function randMdp()
	{
		$comb = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array();
		$combLen = strlen($comb) - 1;

		for ($i = 0; $i < 8; $i++){
			$n = rand(0, $combLen);
			$pass[] = $comb[$n];
		}

		return implode($pass);
	}
}
