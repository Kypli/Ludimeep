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
	public const NUMBER_ITEMS = 9;
	public const NUMBER_ITEMS_TEXT = 6;

	private $file_uploader;

	public function __construct(FileUploader $file_uploader)
	{
		$this->file_uploader = $file_uploader;
	}

	/**
	 * @Route("/", name="", methods={"GET"})
	 */
	public function index(ActuRepository $actuRepository): Response
	{
		return $this->render('actu/index.html.twig', [
			'actus' => $actuRepository->findBy([], ['id' => 'DESC']),
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/add", name="_add", methods={"GET", "POST"})
	 */
	public function add(Request $request, ActuRepository $actuRepository): Response
	{
		$actu = new Actu();
		$form = $this->createForm(ActuType::class, $actu);
		$form
			->remove('auteur')
			->remove('date')
		;
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){

			// SetForm
			$actu = $this->setForm($actu, $form->getData(), $request, $form);

			// Date + auteur
			$actu
				->setAuteur($this->getUser())
				->setDate(new \Datetime('now'))
			;

			if ($actu !== false){
				$actuRepository->add($actu);
				return $this->redirectToRoute('actu_show', ['id' => $actu->getId()], Response::HTTP_SEE_OTHER);
			}
		}

		return $this->renderForm('actu/add.html.twig', [
			'actu' => $actu,
			'form' => $form,
			'metaDatas' => [
				'ordre' => $this->getMetaDatas_ordre($actu->getOrdre()),
				'class' => $this->getMetaDatas_class($actu),
			],
		]);
	}

	/**
	 * @Route("/{id}", name="_show", methods={"GET"})
	 */
	public function show(Actu $actu): Response
	{
		return $this->render('actu/show.html.twig', [
			'actu' => $actu,
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

			// SetForm
			$actu = $this->setForm($actu, $form->getData(), $request, $form);

			if ($actu !== false){
				$actuRepository->add($actu);
				return $this->redirectToRoute('actu_show', ['id' => $actu->getId()], Response::HTTP_SEE_OTHER);
			}
		}

		// M??ta-datas
		$metaDatas['ordre'] = $this->getMetaDatas_ordre($actu->getOrdre());
		$metaDatas['class'] = $this->getMetaDatas_class($actu);

		return $this->renderForm('actu/edit.html.twig', [
			'actu' => $actu,
			'form' => $form,
			'metaDatas' => $metaDatas,
		]);
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * @Route("/{id}", name="_delete", methods={"POST"})
	 */
	public function delete(Request $request, Actu $actu, ActuRepository $actuRepository): Response
	{
		if ($this->isCsrfTokenValid('delete'.$actu->getId(), $request->request->get('_token'))){
			$this->addFlash('success', "L'actualit?? a bien ??t?? supprim??e.");
			$actuRepository->remove($actu);
			$this->file_uploader->cleanPhotosActu($actuRepository);
		}

		return $this->redirectToRoute('actu', [], Response::HTTP_SEE_OTHER);
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

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * Traite le formulaire et ses m??ta-datas
	 */
	public function setForm($actu, $datas, $request, $form)
	{
		// Initialise
		$ordre = [];

		// Boucle sur les textes
		for ($i = 1; $i <= 6; $i++){

			// Text
			$text = 'getText'.$i;
			if (null != $datas->$text()){

				// Ordre text
				$ordre[$this->ordreValide($ordre, $request->request->get('ordre_t'.$i))] = 't'.$i;

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

			// Garder intact une photo si keep on
			if (null !== $request->request->get('photo_keep_'.$i)){

				// Ordre photo
				$ordre[$this->ordreValide($ordre, $request->request->get('ordre_p'.$i))] = 'p'.$i;

			// Si nouvelle photo
			} elseif (null != $form['photo'.$i]->getData()){

				$file = $form['photo'.$i]->getData();
				if ($file){
					$file_name = $this->file_uploader->upload($file, 'actu');
					if (null !== $file_name){
						// $directory = $this->file_uploader->getTargetDirectory();
						// $full_path = $directory.'/'.$file_name;

						$text = 'setPhoto'.$i;
						$actu->$text($file_name);

						// Ordre photo
						$ordre[$this->ordreValide($ordre, $request->request->get('ordre_p'.$i))] = 'p'.$i;

					} else {
						$this->addFlash('error', "Erreur d'upload de l'image ".$i.".");
						return false;
					}
				}

			// Delete photo
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

		return $actu;
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * R??cup??re la liste des ??l??ments ?? afficher dans l'ordre
	 */
	public function getMetaDatas_ordre($ordre){

		$metaDatas_ordre = [];
		$ordre_array = !empty($ordre)
			? explode('_', $ordre)
			: []
		;

		// Boucle sur les 9 ??l??ments
		for ($i = 1; $i <= self::NUMBER_ITEMS; $i++){

			// Textes/Images existants
			if (array_key_exists($i - 1, $ordre_array)){
				$metaDatas_ordre[$ordre_array[$i - 1]] = $i;

			// Textes/Images par d??faut
			} else {

				$img = true;

				// TEXT
				for ($ii = 1; $ii <= self::NUMBER_ITEMS_TEXT; $ii++){
					if (!array_key_exists('t'.$ii, $metaDatas_ordre)){
						$metaDatas_ordre['t'.$ii] = $i;
						$img = false;
						break;
					}
				}

				// IMG
				if ($img){
					$end_img = self::NUMBER_ITEMS - self::NUMBER_ITEMS_TEXT;
					for ($ii = 1; $ii <= $end_img; $ii++){
						if (!array_key_exists('p'.$ii, $metaDatas_ordre)){
							$metaDatas_ordre['p'.$ii] = $i;
							break;
						}
					}
				}
			}
		}

		return $metaDatas_ordre;
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * R??cup??re la liste des class des ??l??ments ?? afficher
	 */
	public function getMetaDatas_class($actu){

		$metaDatas_class = [];

		// Boucle sur les ??l??ments text
		for ($i = 1; $i <= self::NUMBER_ITEMS_TEXT; $i++){

			$func = 'getText'.$i.'Class';
			$class_array = !empty($actu->$func())
				? explode(' ', $actu->$func())
				: []
			;

			$metaDatas_class[$i] = $class_array;
		}

		return $metaDatas_class;
	}

	/**
	 * @IsGranted("ROLE_ADMIN")
	 * Controle et renvoie l'ordre d'un ??l??ment
	 */
	public function ordreValide($ordre, $req){

		// Si doublon
		if (array_key_exists($req, $ordre)){

			// On prend le chiffre disponible suivant
			for($i = $req + 1; $i <= self::NUMBER_ITEMS; $i++){
				if (!array_key_exists($i, $ordre)){
					return $i;
				}
			}

			// Sinon retour ?? 1
			for($i = 1; $i <= self::NUMBER_ITEMS; $i++){
				if (!array_key_exists($i, $ordre)){
					return $i;
				}
			}
		}

		return $req;
	}
}
