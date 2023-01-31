<?php

namespace App\Repository;

use App\Entity\User;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, User::class);
	}

	/**
	 * Used to upgrade (rehash) the user's password automatically over time.
	 */
	public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
	{
		if (!$user instanceof User) {
			throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
		}

		$user->setPassword($newEncodedPassword);
		$this->_em->persist($user);
		$this->_em->flush();
	}

	/**
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function add(User $entity, bool $flush = true): void
	{
		$this->_em->persist($entity);
		if ($flush) {
			$this->_em->flush();
		}
	}

	/**
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function remove(User $entity, bool $flush = true): void
	{
		$this->_em->remove($entity);
		if ($flush) {
			$this->_em->flush();
		}
	}

	/**
	 * @return Renvoie le nombre d'admin
	 */
	public function countAdmin()
	{
		return $this->createQueryBuilder('u')
			->where('u.roles LIKE :role')
			->setParameter('role', '%ROLE_ADMIN%')
			->select('COUNT(u.id)')
			->getQuery()
			->getSingleScalarResult()
		;
	}

	/**
	 * @return Renvoie le nombre d'admin
	 */
	public function getAdminsId()
	{
		$q = $this->createQueryBuilder('u')
			->where('u.roles LIKE :role')
			->setParameter('role', '%ROLE_ADMIN%')
			->select('u.id')
			->groupBy('u.id')
			->getQuery()
			->getResult()
		;

		return array_map('current', $q);
	}

	/**
	 * @return Renvoie le nombre d'anonyme
	 */
	public function countAnonymous()
	{
		return $this->createQueryBuilder('u')
			->where('u.anonyme =  :anonyme')
			->setParameter('anonyme', true)
			->select('COUNT(u.id)')
			->getQuery()
			->getSingleScalarResult()
		;
	}

	/**
	 * @return Renvoie les users, trié par leur rôle CA puis ID
	 */
	public function byRoleCaAndId()
	{
		return $this->createQueryBuilder('u')
			->join('u.asso', 'a')

			->orderBy('a.roleCa', 'ASC')
			->addOrderBy('u.id', 'ASC')

			->getQuery()
			->getResult()
		;
	}
}
