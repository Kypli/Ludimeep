<?php

namespace App\DataFixtures;

use App\Entity\Mandat as Entity;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;


class MandatFixtures extends Fixture implements FixtureGroupInterface
{
    public const PRESIDENT = 'president';

	public function load(ObjectManager $manager)
	{
		$datas = [
			0 => [
				'titre' => "Président",
				'duree' => 3,
				'priorite' => 1,
				'required' => 1,
				'uniq' => 1,
				'isActif' => 1,
			],
			1 => [
				'titre' => "Trésorier",
				'duree' => 3,
				'priorite' => 3,
				'required' => 1,
				'uniq' => 1,
				'isActif' => 1,
			],
			2 => [
				'titre' => "Secrétaire",
				'duree' => 3,
				'priorite' => 5,
				'required' => 1,
				'uniq' => 1,
				'isActif' => 1,
			],
			3 => [
				'titre' => "Président-adjoint",
				'duree' => 1,
				'priorite' => 2,
				'required' => 0,
				'uniq' => 0,
				'isActif' => 1,
			],
			4 => [
				'titre' => "Trésorier-adjoint",
				'duree' => 1,
				'priorite' => 4,
				'required' => 0,
				'uniq' => 0,
				'isActif' => 1,
			],
			5 => [
				'titre' => "Secrétaire-adjoint",
				'duree' => 1,
				'priorite' => 6,
				'required' => 0,
				'uniq' => 0,
				'isActif' => 1,
			],
			6 => [
				'titre' => "Animateur",
				'duree' => 1,
				'priorite' => 7,
				'required' => 0,
				'uniq' => 0,
				'isActif' => 1,
			],
		];
		foreach ($datas as $key => $value){

			$entity = new Entity();
			$entity
				->setTitre($value['titre'])
				->setDuree($value['duree'])
				->setPriorite($value['priorite'])
				->setRequired($value['required'])
				->setUniq($value['uniq'])
				->setIsActif($value['isActif'])
			;
			if ($value['titre'] == 'Président'){
				$this->addReference(self::PRESIDENT, $entity);
			}
			$manager->persist($entity);
		}

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['start'];
	}
}
