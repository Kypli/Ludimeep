<?php

namespace App\Controller\Asso;

use Symfony\Component\Finder\Finder;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/statuts", name="statuts")
 */
class StatutsController extends AbstractController
{	
	public function __construct($statuts_files)
	{
		$this->statuts_files = $statuts_files;
	}

	/**
	 * @Route("/", name="")
	 */
	public function index(Request $request)
	{
		$files = [];
		$finder = new Finder();
		$finder->files()->in($this->statuts_files);

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

		return $this->render('asso/statuts/index.html.twig', [
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

		return $this->file($this->statuts_files.$fileName);
	}
}
