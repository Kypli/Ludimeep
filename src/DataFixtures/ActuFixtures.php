<?php

namespace App\DataFixtures;

use App\Entity\Actu as Entity;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ActuFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
	public function load(ObjectManager $manager)
	{
		$entity = new Entity();
		$entity
			->setTitre("Ouverture de l'association")
			->setAuteur($this->getReference(UserFixtures::USER_PIERRE))
			->setDate(new \Datetime('2022-03-20 10:30:00'))
			->setText1("Bonjour et bienvenue sur le site de l'association de jeux de société : Ludi-Meep'")
			->setText2("Nous serons ravis de vous accueillir pour notre ouverture prochaine.")
			->setText3("le 19 avril 2022 à partir de 18h30<br/>4 Rue Jean Bordier, 45130 Baule. Batiment annexe gauche de la mairie")
			->setText3Class("gras entoure")
			->setText4("Et ensuite hebdomadairement tout les mardis à 18h30.")
			->setPhoto1("flyerOuverture.jpg")
			->setPhoto1Alt("Flyer de l'ouverture")
			->setOrdre("t1_t2_t3_t4_p1")
		;

		$manager->persist($entity);
		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['start'];
	}

	public function getDependencies()
	{
		return [
			UserFixtures::class,
		];
	}
}
