<?php

namespace App\Controller;

use App\Entity\Actu;
use App\Form\ActuType;
use App\Service\FileUploader;
use App\Repository\ActuRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/actu", name="actu")
 */
class ActuController extends AbstractController
{
	/**
	 * @Route("/", name="_index", methods={"GET"})
	 */
	public function index(ActuRepository $actuRepository): Response
	{
		return $this->render('actu/index.html.twig', [
			'actus' => $actuRepository->findAll(),
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/add", name="_add", methods={"GET", "POST"})
	 */
	public function add(Request $request, ActuRepository $actuRepository, FileUploader $file_uploader): Response
	{
		$actu = new Actu();
		$form = $this->createForm(ActuType::class, $actu);
		$form
			->remove('auteur')
			->remove('date')
		;
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){

			// Initialise
			$ordre = [];
			$datas = $form->getData();

			// Boucle sur les textes
			for ($i = 1; $i <= 6; $i++){

				// Text
				$text = 'getText'.$i;
				if (null != $datas->$text()){

					// Ordre text
					$ordre[$request->request->get('ordre_t'.$i)] = 't'.$i;

					// TextClass
					$textClass = '';

					// Gras
					if (null !== $request->request->get('gras_'.$i) && $request->request->get('gras_'.$i) == 'on'){
						$textClass .= 'gras ';
					}

					// Italique
					if (null !== $request->request->get('italique_'.$i) && $request->request->get('italique_'.$i) == 'on'){
						$textClass .= 'italique ';
					}

					// Entoure
					if (null !== $request->request->get('entoure_'.$i) && $request->request->get('entoure_'.$i) == 'on'){
						$textClass .= 'entoure ';
					}

					// Gros
					if (null !== $request->request->get('gros_'.$i) && $request->request->get('gros_'.$i) == 'on'){
						$textClass .= 'taille25 ';
					}

					// Couleur
					if ('Noir' != $request->request->get('couleur_'.$i)){
						$textClass .= $request->request->get('couleur_'.$i).' ';
					}

					// Retire l'espace de fin
					if (!empty($textClass)){ $textClass = substr($textClass, 0, -1); }

					// Save textClass
					$text = 'setText'.$i.'Class';
					$actu->$text($textClass);
				}
			}

			// Boucle sur les photos
			for ($i = 1; $i <= 3; $i++){

				// Photo
				if (null != $form['photo'.$i]->getData()){

					$file = $form['photo'.$i]->getData();
					if ($file){
						$file_name = $file_uploader->upload($file);
						if (null !== $file_name){
							// $directory = $file_uploader->getTargetDirectory();
							// $full_path = $directory.'/'.$file_name;

							$text = 'setPhoto'.$i;
							$actu->$text($file_name);

							// Ordre photo
							$ordre[$request->request->get('ordre_p'.$i)] = 'p'.$i;

						} else {
							$error = true;
							$this->addFlash('error', "Erreur d'upload de l'image ".$i.".");
						}
					}
				} else {
					$text = 'setPhoto'.$i;
					$actu->$text(null);
					$text = 'setPhoto'.$i.'Alt';
					$actu->$text(null);
				}
			}

			// Ordre
			ksort($ordre);
			$ordre_text = '';
			foreach ($ordre as $value){
				$ordre_text .= $value.'_';
			}

			// Retire l'espace de fin
			if (!empty($ordre_text)){ $ordre_text = substr($ordre_text, 0, -1); }
			$actu->setOrdre($ordre_text);

			// Date + auteur
			$actu
				->setAuteur($this->getUser())
				->setDate(new \Datetime('now'))
			;

			if (!isset($error)){
				$actuRepository->add($actu);
				return $this->redirectToRoute('actu_show', ['id' => $actu->getId()], Response::HTTP_SEE_OTHER);
			}
		}

		return $this->renderForm('actu/add.html.twig', [
			'actu' => $actu,
			'form' => $form,
		]);
	}

	/**
	 * @Route("/{id}", name="_show", methods={"GET"})
	 */
	public function show(Actu $actu): Response
	{
		return $this->render('actu/show.html.twig', [
			'actu' => $actu,
			'actus' => [$actu],
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/{id}/edit", name="_edit", methods={"GET", "POST"})
	 */
	public function edit(Request $request, Actu $actu, ActuRepository $actuRepository): Response
	{
		$form = $this->createForm(ActuType::class, $actu);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){
			// $actuRepository->add($actu);
			return $this->redirectToRoute('actu_actu', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('actu/edit.html.twig', [
			'actu' => $actu,
			'form' => $form,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/{id}", name="_delete", methods={"POST"})
	 */
	public function delete(Request $request, Actu $actu, ActuRepository $actuRepository): Response
	{
		if ($this->isCsrfTokenValid('delete'.$actu->getId(), $request->request->get('_token'))) {
			$actuRepository->remove($actu);
		}

		return $this->redirectToRoute('actu_index', [], Response::HTTP_SEE_OTHER);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/{id}/valid", name="_valid")
	 */
	public function valid(Request $request, Actu $actu, ActuRepository $actuRepository): Response
	{
		$actu->setValid(!$actu->getValid());
		$actuRepository->add($actu);

		return $this->redirectToRoute('actu_show', ['id' => $actu->getId()], Response::HTTP_SEE_OTHER);
	}
}
