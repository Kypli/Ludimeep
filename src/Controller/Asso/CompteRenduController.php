<?php

namespace App\Controller\Asso;

use Symfony\Component\Finder\Finder;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/cr", name="cr")
 */
class CompteRenduController extends AbstractController
{	
	public function __construct($cr_files)
	{
		$this->cr_files = $cr_files;
	}

	/**
	 * @Route("/", name="")
	 */
	public function index(Request $request)
	{
		$directories = [];
		$finder = new Finder();
		$finder->files()->in($this->cr_files);

		foreach ($finder as $file){

			if (
				$file != '..' &&
				$file !='.' &&
				$file != '' &&
				$file != '.htaccess' &&
				$file!='.gitignore' &&
				$file!='.hgignore'
			){
				$directories[$file->getRelativePath()][] = $file->getFileName();
			}
		}
		ksort($directories);

		return $this->render('asso/cr/index.html.twig', [
			'dirs' => $directories,
		]);
	}

	/**
	 * Met en forme le titre du dossier
	 */
	public function dirTitle($title)
	{
		$str = explode('_', $title);
		$annee = substr($str[0], 0, 4);
		$mois = substr($str[0], 4, 2);
		$jour = substr($str[0], 6, 2);

		return new Response(ucfirst($str[1]).' du '.$jour.'/'.$mois.'/'.$annee);
	}

	/**
	 * @Route("/export/{dir}/{fileName}", name="_export")
	 */
	public function export($dir, $fileName)
	{
		// Controle
		if ($fileName == '' || $fileName == null){
			dump('Erreur de liens.');
			die;
		}

		return $this->file($this->cr_files.$dir.'/'.$fileName);
	}
}
