<?php

namespace App\Repository;

use App\Entity\Operation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Operation>
 *
 * @method Operation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Operation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Operation[]    findAll()
 * @method Operation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Operation::class);
	}

	public function add(Operation $entity, bool $flush = false): void
	{
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Operation $entity, bool $flush = false): void
	{
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	/**
	 * @return Get user's solde
	 */
	public function solde($user_id)
	{
		return $this->createQueryBuilder('x')
			->join('x.user', 'u')

			->select('SUM(x.number)')

			->where('u.id = :user_id')
			->andWhere('x.valid = true')

			->setParameters([
				'user_id' => $user_id,
			])

			->getQuery()
			->getSingleScalarResult()
		;
	}

	/**
	 * @return Get all opérations not valid
	 */
	public function encours()
	{
		return $this->createQueryBuilder('x')
			->select('COUNT(x)')
			->where('x.valid = false')

			->getQuery()
			->getSingleScalarResult()
		;
	}

	/**
	 * @return Get user's opérations not valid
	 */
	public function encoursByUser($user_id)
	{
		return $this->createQueryBuilder('x')
			->join('x.user', 'u')
			
			->select('COUNT(x)')
			->where('x.valid = false')
			->andWhere('u.id = :user_id')

			->setParameters([
				'user_id' => $user_id,
			])

			->getQuery()
			->getSingleScalarResult()
		;
	}
}
