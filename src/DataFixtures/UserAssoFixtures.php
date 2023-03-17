<?php

namespace App\DataFixtures;

use App\Entity\UserAsso as Entity;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class UserAssoFixtures extends Fixture implements FixtureGroupInterface
{
	public function load(ObjectManager $manager)
	{
		// Admin
		$entity = new Entity();
		$entity
			->setUser($this->getReference(UserFixtures::USER_ADMIN))
			->setDroitImage(true)
			->setAdherant(1)
			->setDateInscription(new \Datetime('2022-04-19'))
			->setDateFinAdhesion(new \Datetime('2023-07-01'))
			->setNotoriete('Membre fondateur')
			->setMandat($this->getReference(MandatFixtures::PRESIDENT))
			->setDateFinMandat(new \Datetime('2025-07-01'))
			->setMembreHonneur(false)
		;
		$manager->persist($entity);

		// User
		$entity = new Entity();
		$entity
			->setUser($this->getReference(UserFixtures::USER_USER))
			->setDroitImage(false)
			->setAdherant(2)
			->setDateInscription(new \Datetime('2022-04-19'))
			->setDateFinAdhesion(new \Datetime('2023-07-01'))
			->setNotoriete('Coucou')
			->setMembreHonneur(false)
		;
		$manager->persist($entity);

		$manager->flush();
	}

	public function getDependencies()
	{
		return [
			UserFixtures::class,
		];
	}

	public static function getGroups(): array
	{
		return ['test'];
	}
}
