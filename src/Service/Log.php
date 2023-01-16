<?php

namespace App\Service;

use App\Entity\Tchat;
use App\Repository\TchatRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Enregistre les actions des utilisateurs pour historique
 */
class Log extends AbstractController
{
	public const PHOTO = 1;
	public const ACTU = 2;
	public const TABLE = 3;
	public const GAME = 4;
	public const SONDAGE = 5;
	public const NEWSLETTER = 6;

	private $date;
	private $tr;

	public function __construct(TchatRepository $tr)
	{
		$this->date = new \Datetime('now');
		$this->tr = $tr;
	}

	/**
	 * Enregistre un log
	 * $action: Récupère la onstante de l'action
	 * $cible: Récupère la cible concerné de l'action
	 */
	public function saveLog($action, $cible = null)
	{
		// Set datas
		$log = new Tchat;
		$log
			->setUser(null)
			->setContent($this->text($action, $cible))
			->setDate($this->date)
		;

		// Doublon ?
		if ($this->doublon($log->getContent())){ return false; } 
		
		// Enregistre
		$this->tr->add($log, true);

		return true;
	}

	/**
	 * Renvoie le text du log
	 */
	public function text($action, $cible)
	{
		$user = $this->getUser();
		$uPro = $user->getProfil();
		$text = !empty($uPro->getNom()) && !empty($uPro->getPrenom())
			? "<span class='help' title='".strtoupper($uPro->getNom())." ".ucfirst($uPro->getPrenom())."'>".ucfirst($user->getUserName())."</span> "
			: ucfirst($user->getUserName())." "
		;

		switch ($action){
			case 1:
				$text .= "vient de rajouter une photo (<a href='".$this->generateUrl('photo')."'>ici</a>)";
				break;

			case 2:
				$text .= "vient d'écrire une actualité (<a href='".$this->generateUrl('actu').$cible."'>ici</a>)";
				break;

			case 3:
				$text .= "vient d'ouvrir une table de jeu : '".$cible."'";
				break;

			case 4:
				$text .= "vient de rajouter un jeu à sa collection : '".$cible."'";
				break;

			case 5:
				$text .= "vient de créer un sondage : '".$cible."'";
				break;

			case 6:
				$text .= "vient de lancer une newsletter : '".$cible."'";
				break;
			
			default:
				$text .= "passe par là.";
				break;
		}

		return $text;
	}

	/**
	 * Contrôle les doublons
	 */
	public function doublon($content)
	{
		$doublon = $this->tr->findByContent($content);
		
		if (count($doublon) == 0){
			return false;
		} else {
			return true;
		}
	}
}
