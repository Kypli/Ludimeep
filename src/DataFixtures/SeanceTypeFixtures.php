<?php

namespace App\DataFixtures;

use App\Entity\SeanceType as Entity;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;


class SeanceTypeFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager)
	{
		$datas = [
			1 => "Séance régulière",
			2 => "Séance mensuelle",
			3 => "Séance exceptionnelle",
			4 => "Tournoi",
			5 => "Animation",
			6 => "Animation thématique",
			7 => "Animation extérieur",
			8 => "Festive",
			9 => "Assemblée générale",
			10 => "Assemblée générale exceptionnelle",
			11 => "Réunion du bureau",
		];
		foreach ($datas as $key => $value){

			$entity = new Entity();
			$entity->setName($value);

			$this->addReference('seanceType_'.$key, $entity);
			$manager->persist($entity);
		}
		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['start'];
	}
}
