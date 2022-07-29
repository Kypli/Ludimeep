<?php

namespace App\DataFixtures;

use App\Entity\User as Entity;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public const USER_PIERRE = 'user-pierre';
    public const USER_SANDRINE = 'user-sandrine';
    public const USER_ESTELLE = 'user-estelle';

	private $passwordHasher;

	public function __construct(UserPasswordHasherInterface $passwordHasher)
	{
		$this->passwordHasher = $passwordHasher;
	}

	public function load(ObjectManager $manager)
	{
		// Pierre
		$entity = new Entity();
		$entity
			->setUserName('kyp')
			->setPassword($this->passwordHasher->hashPassword(
				$entity,
				'mdp'
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
		$this->addReference(self::USER_PIERRE, $entity);
		$manager->persist($entity);

		// Sandrine
		$entity = new Entity();
		$entity
			->setUserName('sandrine')
			->setPassword($this->passwordHasher->hashPassword(
				$entity,
				'mdp'
			))
			->setRoles(["ROLE_ADMIN"])
			->setDroitImage(false)
			->setNewsletter(false)
			->setNom('duchon')
			->setPrenom('sandrine')
			->setMail('duchon.sandrine@protonmail.com')
			->setAdherant('2')
			->setDateInscription(new \Datetime('2022-04-19'))
			->setDateFinAdhesion(new \Datetime('2023-07-01'))
			->setNotoriete('Membre fondateur')
			->setRoleCa('Trésorière')
			->setDateFinMandat(new \Datetime('2025-07-01'))
			->setMembreHonneur(false)
		;
		$this->addReference(self::USER_SANDRINE, $entity);
		$manager->persist($entity);

		// Estelle
		$entity = new Entity();
		$entity
			->setUserName('estelle')
			->setPassword($this->passwordHasher->hashPassword(
				$entity,
				'mdp'
			))
			->setRoles(["ROLE_ADMIN"])
			->setDroitImage(true)
			->setNewsletter(true)
			->setNom('ridel')
			->setPrenom('estelle')
			->setMail('pierre.amboise@yahoo.fr')
			->setAdresse('10 rue du clos drouard, 45740 lailly-en-val')
			->setTelephone('06 27 20 13 12')
			->setAdherant('3')
			->setDateInscription(new \Datetime('2022-04-19'))
			->setDateFinAdhesion(new \Datetime('2023-07-01'))
			->setNotoriete('Membre fondateur')
			->setRoleCa('Secrétaire')
			->setDateFinMandat(new \Datetime('2025-07-01'))
			->setMembreHonneur(false)
		;
		$this->addReference(self::USER_ESTELLE, $entity);
		$manager->persist($entity);

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['start'];
	}
}
