<?php

namespace App\DataFixtures;

use App\Entity\User as Entity;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
	private $passwordHasher;

	public function __construct(UserPasswordHasherInterface $passwordHasher)
	{
		$this->passwordHasher = $passwordHasher;
	}

	public function load(ObjectManager $manager)
	{
		// Admin
		$entity = new Entity();
		$entity
			->setUserName('kyp')
			->setPassword($this->passwordHasher->hashPassword(
				$entity,
				'admin741'
			))
			->setRoles(["ROLE_ADMIN"])
			->setDroitImage(true)
			->setNewsletter(true)
			->setNom('amboise')
			->setPrenom('pierre')
			->setMail('pierre.amboise@yahoo.fr')
			->setAdresse('10 rue du clos drouard, 45740 lailly-en-val')
			->setTelephone('06 27 95 04 89')
			->setAdherant('1')
			->setDateInscription(new \Datetime('2022-04-19'))
			->setDateFinAdhesion(new \Datetime('2023-07-01'))
			->setNotoriete('Membre fondateur')
			->setRoleCa('Président')
			->setDateFinMandat(new \Datetime('2025-07-01'))
			->setMembreHonneur(false)
		;

		$manager->persist($entity);
		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['pierre'];
	}
}
