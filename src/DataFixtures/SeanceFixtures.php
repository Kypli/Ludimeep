<?php

namespace App\DataFixtures;

use App\Entity\Seance as Entity;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SeanceFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
	public function load(ObjectManager $manager)
	{
		$dateBlock = [
			1 => 11,
			2 => 29,
		];

		for ($i = 1; $i <= 50; $i++){

			unset($date);
			$date = new \Datetime('2022-04-12 18:30:00');
			$date->modify("+".$i." week");

			if (!in_array($i, $dateBlock)){
				$entity = new Entity();
				$entity
					->setDate($date)
					->setDuree(new \Datetime('4:30:00'))
					->setType($this->getReference('seanceType_1'))
				;
				$manager->persist($entity);
			}
		}
		$manager->flush();
	}

	public function getDependencies()
	{
		return [
			SeanceTypeFixtures::class,
		];
	}

	public static function getGroups(): array
	{
		return ['start'];
	}
}
