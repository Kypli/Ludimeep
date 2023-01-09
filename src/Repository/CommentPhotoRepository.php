<?php

namespace App\Repository;

use App\Entity\CommentPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommentPhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentPhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentPhoto[]    findAll()
 * @method CommentPhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentPhotoRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, CommentPhoto::class);
	}

	/**
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function add(CommentPhoto $entity, bool $flush = true): void
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
	public function remove(CommentPhoto $entity, bool $flush = true): void
	{
		$this->_em->remove($entity);
		if ($flush) {
			$this->_em->flush();
		}
	}
}
