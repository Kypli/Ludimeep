<?php

namespace App\Controller\Contact;

use App\Entity\Message;
use App\Entity\Discussion;

use App\Form\MessageType as MessageForm;
use App\Form\DiscussionType as DiscussionForm;

use App\Controller\UserController;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use App\Repository\DiscussionRepository;

use App\Service\Discussion as DiscussionSer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/discussion", name="discussion")
 */
class DiscussionController extends AbstractController
{
	/**
	 * @IsGranted("ROLE_USER")
	 * @Route("/", name="", methods={"GET"})
	 */
	public function index(DiscussionRepository $dr, MessageRepository $mr): Response
	{
		$datas = [];
		$user = $this->getUser();
		$discussions = $dr->getDiscussions($user);

		foreach($discussions as $key => $discussion){
			$datas[$key]['nonLu'] = (int) $mr->getMessagesNonLu($discussion, $user);
			$datas[$key]['nbreMessage'] = $mr->countMessagesInDiscussion($discussion);
		}

		return $this->render('contact/discussion/index.html.twig', [
			'discussions' => $discussions,
			'datas' => $datas,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/admin", name="_admin", methods={"GET"})
	 */
	public function indexAdmin(DiscussionRepository $dr, MessageRepository $mr): Response
	{
		$datas = [];
		$datas_admin = [];
		$user = $this->getUser();
		$discussions = $dr->getDiscussionsExceptAdmins();
		$discussions_admin = $dr->findBy(['destinataire' => null], ['date' => 'DESC']);

		foreach($discussions as $key => $discussion){
			$datas[$key]['nonLu'] = (int) $mr->getMessagesNonLu($discussion, $user);
			$datas[$key]['nbreMessage'] = $mr->countMessagesInDiscussion($discussion);
		}

		foreach($discussions_admin as $key => $discussion){
			$datas_admin[$key]['nonLu'] = (int) $mr->getMessagesNonLuAdmin($discussion);
			$datas_admin[$key]['nbreMessage'] = $mr->countMessagesInDiscussion($discussion);
		}

		return $this->render('contact/discussion/index_admin.html.twig', [
			'datas' => $datas,
			'datas_admin' => $datas_admin,
			'discussions' => $discussions,
			'discussions_admin' => $discussions_admin,
		]);
	}

	/**
	 * @Route("/add", name="_add", methods={"GET", "POST"})
	 */
	public function add(DiscussionRepository $dr, MessageRepository $mr, UserController $uc, Request $request): Response
	{
		$message = new Message();
		$discussion = new Discussion();
		$form = $this->createForm(MessageForm::class, $message);

		if ($this->getUser() != null){
			$form->remove('mail');
		}
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){

			$message
				->setDate(new \Datetime('now'))
				->setUser($this->getUser())
				->setDiscussion($discussion)
			;

			$discussion
				->setAuteur($this->getUser())
				->setLibelle($request->request->get('message')['libelle'])
				->setDate(new \Datetime('now'))
			;

			// Anonyme
			if ($this->getUser() == null){
				$newUser = $uc->addAnonyme($request->request->get('message')['mail'], $request->getClientIp());
				$message->setUser($newUser['user']);
				$discussion->setAuteur($newUser['user']);

				$textSucess = [
					'Votre message a bien été enregistré',
					'<br />',
					'Une réponse vous sera envoyé à votre adresse mail: '.$request->request->get('message')['mail'],
					'<br />',
					'Ou alors dans la section "Mes messages" en utilisant ce compte',
					'<br />',
					 'Login: '.$newUser['login'],
					'<br />',
					 'Mot de passe: '.$newUser['mdp'],
				];

				$this->addFlash('success', implode($textSucess));

			// User
			} else {
				$this->addFlash('success', "Votre message a bien été enregistré.");
			}

			$dr->add($discussion);
			$mr->add($message);

			return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('contact/discussion/add.html.twig', [
			'form' => $form,
			'libelle' => true,
			'message' => $message,
		]);
	}

	/**
	 * @IsGranted("ROLE_USER")
	 * @Route("/{id}", name="_show")
	 */
	public function show(
		Discussion $discussion,
		DiscussionRepository $dr,
		MessageRepository $mr,
		DiscussionSer $discussionSer,
		Request $request): Response
	{
		$user_id = $this->getUser()->getId();

		// Autorisation accès
		if (
			(
				$discussion->getDestinataire() == null &&
				!$this->isGranted('ROLE_ADMIN') &&
				$discussion->getAuteur()->getID() != $user_id
			) ||
			(
				$discussion->getAuteur()->getId() != $user_id &&
				(
					$discussion->getDestinataire() != null &&
					$discussion->getDestinataire()->getId() != $user_id &&
					!$this->isGranted('ROLE_ADMIN')
				)
			)
		){
			return $this->redirectToRoute('discussion', [], Response::HTTP_SEE_OTHER);
		}

		// Lecture des messages
		foreach($discussion->getMessages() as $message){
			if (
				$message->getUser()->getId() != $user_id ||
				(
					$this->isGranted('ROLE_ADMIN') &&
					$discussion->getAuteur()->getId() == $user_id &&
					$message->getUser()->getId() == $user_id
				)
			){
				$message->setLu(true);
				$mr->add($message);
			}
		}

		// Maj des sessions vu
		$discussionSer->update();


		$message = new Message();
		$form = $this->createForm(MessageForm::class, $message);

		$form->remove('libelle');
		$form->remove('mail');

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){

			$message
				->setDate(new \Datetime('now'))
				->setUser($this->getUser())
				->setDiscussion($discussion)
			;
			$mr->add($message);

			$discussion->addMessage($message);
			$dr->add($discussion);
		}

		return $this->renderForm('contact/discussion/show.html.twig', [
			'form' => $form,
			'd' => $discussion,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/edit/{id}", name="_edit", methods={"GET", "POST"})
	 */
	public function edit(Discussion $discussion, DiscussionRepository $dr, Request $request): Response
	{
		$form = $this->createForm(DiscussionForm::class, $discussion);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){
			$dr->add($discussion);
			return $this->redirectToRoute('discussion', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('contact/discussion/edit.html.twig', [
			'form' => $form,
			'discussion' => $discussion,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/edit/message/{id}", name="_message_edit", methods={"GET", "POST"})
	 */
	public function editMessage(Message $message, MessageRepository $mr, Request $request): Response
	{
		$form = $this->createForm(MessageForm::class, $message);
		$form->remove('mail');
		$form->remove('libelle');
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$mr->add($message);
			return $this->redirectToRoute('discussion', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('contact/discussion/edit_message.html.twig', [
			'message' => $message,
			'libelle' => false,
			'form' => $form,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/delete/{id}", name="_delete", methods={"POST"})
	 */
	public function delete(Discussion $discussion, DiscussionRepository $dr, Request $request): Response
	{
		if ($this->isCsrfTokenValid('delete'.$discussion->getId(), $request->request->get('_token'))){
			$dr->remove($discussion);
		}

		return $this->redirectToRoute('discussion_admin', [], Response::HTTP_SEE_OTHER);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/delete/message/{id}", name="_message_delete", methods={"POST"})
	 */
	public function deleteMessage(Request $request, Message $message, MessageRepository $mr, DiscussionRepository $dr): Response
	{
		if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))){

			$discussion = $message->getDiscussion();
			$nbreMessage = $mr->countMessagesInDiscussion($discussion);
			$mr->remove($message);

			if ($nbreMessage - 1 == 0){
				$dr->remove($discussion);
			}
		}

		return $this->redirectToRoute('discussion_admin', [], Response::HTTP_SEE_OTHER);
	}
}
