<?php

namespace App\DataFixtures;

use App\Entity\User as Entity;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public const USER_ADMIN = 'admin';
    public const USER_USER = 'user';

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
			->setUserName('admin')
			->setPassword($this->passwordHasher->hashPassword(
				$entity,
				'admin'
			))
			->setRoles(["ROLE_ADMIN"])
		;
		$this->addReference(self::USER_ADMIN, $entity);
		$manager->persist($entity);

		// User
		$entity = new Entity();
		$entity
			->setUserName('user')
			->setPassword($this->passwordHasher->hashPassword(
				$entity,
				'user'
			))
			->setRoles(["ROLE_USER"])
		;
		$this->addReference(self::USER_USER, $entity);
		$manager->persist($entity);

		$manager->flush();
	}

	public static function getGroups(): array
	{
		return ['test'];
	}
}
