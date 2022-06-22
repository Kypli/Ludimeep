<?php

namespace App\Controller\Contact;

use App\Entity\Message;
use App\Form\MessageType;

use App\Controller\UserController;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;

use App\Service\Discussions;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/message", name="message")
 */
class MessageController extends AbstractController
{
	/**
	 * @IsGranted("ROLE_USER")
	 * @Route("/", name="_index", methods={"GET"})
	 */
	public function index(MessageRepository $mr, UserRepository $ur): Response
	{
		$user_id = $this->getUser()->getId();
		$discussions = $mr->mesDiscussions($this->getUser());

		// Discussions
		foreach ($discussions as $key => $message){

			$discussion = $message['discussion'];

			// Correspondant
			$discussions[$key]['correspondants'][] = $message['user_id'];
			$discussions[$key]['correspondants'][] = $message['destinataire_id'];

			// Nombre de message dans la discussion
			$discussions[$key]['nombre'] = null == $discussion
				? 1
				: (int) $mr->countMessagesInDiscussion($discussion)
			;

			// Si un seul message
			if (null === $discussion){

				// Lu ?
				$discussions[$key]['lu'] = $message['destinataire_id'] == $user_id
					? $message['message_lu']
					: true
				;

				// PLuriel
				$discussions[$key]['pluriel'] = 's';

			} else {

				// Lu
				$discussions[$key]['lu'] = $mr->discussionLu($discussion, $user_id) > 0
					? false
					: true
				;

				// Pluriel
				$discussions[$key]['pluriel'] = '';
			}
		}

		// Correspondants
		foreach($discussions as $key => $discussion){

			// Supprime les doublons
			$discussions[$key]['correspondants'] = array_unique($discussions[$key]['correspondants']);

			foreach($discussion['correspondants'] as $key2 => $correspondant){

				// Supprime l'user
				if ($correspondant == $this->getUser()->getId()){
					unset($discussions[$key]['correspondants'][$key2]);

				// Récupère l'entité du correspondant
				} elseif($correspondant != null) {
					$discussions[$key]['correspondants'][$key2] = $ur->find($correspondant);
				}
			}
		}

		return $this->render('contact/message/index.html.twig', [
			'discussions' => $discussions,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/admin", name="_index_admin", methods={"GET"})
	 */
	public function indexAdmin(MessageRepository $messageRepository): Response
	{
		return $this->render('contact/message/index_admin.html.twig', [
			'messages' => $messageRepository->findBy([], ['date' => 'DESC']),
		]);
	}

	/**
	 * @Route("/add", name="_add", methods={"GET", "POST"})
	 */
	public function add(Request $request, MessageRepository $mr, UserController $uc): Response
	{
		$message = new Message();
		$form = $this->createForm(MessageType::class, $message);

		if ($this->getUser() != null){
			$form->remove('mail');
		}

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){

			// Get last discussion
			$lastDiscussion = $mr->getLastDiscussion();
			$newDiscussionNumber = !empty($lastDiscussion)
				? (int) $lastDiscussion[0]['discussion'] + 1
				: 1
			;

			$message
				->setDate(new \Datetime('now'))
				->setUser($this->getUser())
				->setDiscussion($newDiscussionNumber)
			;

			// Anonyme
			if ($this->getUser() == null){
				$newUser = $uc->addAnonyme($request->request->get('message')['mail'], $request->getClientIp());
				$message->setUser($newUser['user']);

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

			$mr->add($message);


			return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('contact/message/add.html.twig', [
			'message' => $message,
			'form' => $form,
		]);
	}

	/**
	 * @IsGranted("ROLE_USER")
	 * @Route("/{id}", name="_show", methods={"GET"})
	 */
	public function show(Message $message, MessageRepository $messageRepository, Discussions $discussions): Response
	{
		$discussion = $messageRepository->maDiscussion($this->getUser(), $this->isGranted('ROLE_ADMIN'), $message->getDiscussion());

		foreach($discussion as $message){
			if (
				(
					$message->getDestinataire() != null &&
					$message->getDestinataire()->getId() == $this->getUser()->getId()
				) ||
				(
					$message->getDestinataire() == null &&
					$this->isGranted('ROLE_ADMIN')
				)
			){
				$message->setLu(true);
				$messageRepository->add($message);
			}
		}

		$discussions->update();

		return $this->render('contact/message/show.html.twig', [
			'discussion' => $discussion,
			'libelle' => $message->getLibelle(),
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/edit/{id}", name="_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, Message $message, MessageRepository $messageRepository): Response
	{
		$form = $this->createForm(MessageType::class, $message);
		$form->remove('mail');
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$messageRepository->add($message);
			return $this->redirectToRoute('message_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('contact/message/edit.html.twig', [
			'message' => $message,
			'form' => $form,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/delete/{id}", name="_delete", methods={"POST"})
	 */
	public function delete(Request $request, Message $message, MessageRepository $messageRepository): Response
	{
		if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))){
			$messageRepository->remove($message);
		}

		return $this->redirectToRoute('message_index_admin', [], Response::HTTP_SEE_OTHER);
	}
}
