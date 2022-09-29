<?php

namespace App\DataFixtures;

use App\Entity\SeanceLieu as Entity;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;


class SeanceLieuFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager)
	{
		foreach ($datas as $key => $value){

			$entity = new Entity();
			$entity
				->setName('Salle de jeux')
				->setAdresse('4 Rue Jean Bordier')
				->setCodePostal('45130')
				->setVille('Baule')
				->setDefault(true)
			;
			$manager->persist($entity);
		}
		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['start'];
	}
}
