<?php

namespace App\Controller\Contact;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;

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
	 * @Route("/", name="_index", methods={"GET"})
	 */
	public function index(MessageRepository $messageRepository): Response
	{
		return $this->render('contact/message/index.html.twig', [
			'messages' => $messageRepository->findAll(),
		]);
	}

	/**
	 * @Route("/add", name="_add", methods={"GET", "POST"})
	 */
	public function add(Request $request, MessageRepository $messageRepository): Response
	{
		$message = new Message();
		$form = $this->createForm(MessageType::class, $message);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){

			$message
				->setDate(new \Datetime('now'))
				->setUser($this->getUser())
			;

			$messageRepository->add($message);

			$this->addFlash('success', "Votre message a bien été enregistré.");

			return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('contact/message/add.html.twig', [
			'message' => $message,
			'form' => $form,
		]);
	}

	/**
	 * @Route("/{id}", name="_show", methods={"GET"})
	 */
	public function show(Message $message): Response
	{
		return $this->render('contact/message/show.html.twig', [
			'message' => $message,
		]);
	}

	/**
	 * @Route("/{id}/edit", name="_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, Message $message, MessageRepository $messageRepository): Response
	{
		$form = $this->createForm(MessageType::class, $message);
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
	 * @Route("/{id}", name="_delete", methods={"POST"})
	 */
	public function delete(Request $request, Message $message, MessageRepository $messageRepository): Response
	{
		if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
			$messageRepository->remove($message);
		}

		return $this->redirectToRoute('message_index', [], Response::HTTP_SEE_OTHER);
	}
}
