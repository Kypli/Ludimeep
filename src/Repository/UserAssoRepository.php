<?php

namespace App\Repository;

use App\Entity\UserAsso;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAsso>
 *
 * @method UserAsso|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAsso|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAsso[]    findAll()
 * @method UserAsso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAssoRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, UserAsso::class);
	}

	public function add(UserAsso $entity, bool $flush = true): void
	{
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(UserAsso $entity, bool $flush = true): void
	{
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}
}
