<?php

namespace App\Repository;

use App\Entity\CommentActu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommentActu|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentActu|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentActu[]    findAll()
 * @method CommentActu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentActuRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, CommentActu::class);
	}

	/**
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function add(CommentActu $entity, bool $flush = true): void
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
	public function remove(CommentActu $entity, bool $flush = true): void
	{
		$this->_em->remove($entity);
		if ($flush) {
			$this->_em->flush();
		}
	}

	/**
	 * @return Count Aimes
	 */
	public function getAimes($actu_id)
	{

		return $this->createQueryBuilder('x')
			->select('COUNT(x)')

			->where('x.actu = :actu_id')
			->andWhere('x.aime = true')

			->setParameter('actu_id', $actu_id)

			->getQuery()
			->getSingleScalarResult()
		;
	}

	/**
	 * @return Count Thumb up
	 */
	public function getThumbUp($actu_id)
	{

		return $this->createQueryBuilder('x')
			->select('COUNT(x)')

			->where('x.actu = :actu_id')
			->andWhere('x.thumb = true')

			->setParameter('actu_id', $actu_id)

			->getQuery()
			->getSingleScalarResult()
		;
	}

	/**
	 * @return Count Thumb down
	 */
	public function getThumbDown($actu_id)
	{

		return $this->createQueryBuilder('x')
			->select('COUNT(x)')

			->where('x.actu = :actu_id')
			->andWhere('x.thumb = false')

			->setParameter('actu_id', $actu_id)

			->getQuery()
			->getSingleScalarResult()
		;
	}

	/**
	 * @return Count Thumb down
	 */
	public function getCaByUserAndActu($actu_id, $user_id)
	{
		return $this->createQueryBuilder('x')
			->select('x')

			->where('x.actu = :actu_id')
			->andWhere('x.user = :user_id')

			->setParameters([
				'actu_id' => $actu_id,
				'user_id' => $user_id,
			])

			->getQuery()
			->getOneOrNullResult()
		;
	}
}
