<?php

namespace App\Controller\Asso;

use Symfony\Component\Finder\Finder;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/reglement", name="reglement")
 */
class ReglementController extends AbstractController
{
	public function __construct($reglement_files)
	{
		$this->reglement_files = $reglement_files;
	}

	/**
	 * @Route("/", name="")
	 */
	public function index(Request $request)
	{
		$files = [];
		$finder = new Finder();
		$finder->files()->in($this->reglement_files);

		foreach ($finder as $file){

			if (
				$file != '..' &&
				$file !='.' &&
				$file != '' &&
				$file != '.htaccess' &&
				$file!='.gitignore' &&
				$file!='.hgignore'
			){
				$files[] = $file;
			}
		}

		return $this->render('asso/reglement/index.html.twig', [
			'files' => $files,
		]);
	}

	/**
	 * @Route("/export/{fileName}", name="_export")
	 */
	public function export($fileName)
	{
		// Controle
		if ($fileName == '' || $fileName == null){
			dump('Erreur de liens.');
			die;
		}

		return $this->file($this->reglement_files.$fileName);
	}
}
