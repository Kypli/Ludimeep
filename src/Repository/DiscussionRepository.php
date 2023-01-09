<?php

namespace App\Repository;

use App\Entity\Discussion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Discussion>
 *
 * @method Discussion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discussion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discussion[]    findAll()
 * @method Discussion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscussionRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Discussion::class);
	}

	/**
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function add(Discussion $entity, bool $flush = true): void
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
	public function remove(Discussion $entity, bool $flush = true): void
	{
		$this->_em->remove($entity);
		if ($flush) {
			$this->_em->flush();
		}
	}

	/**
	 * @return Renvoie les discussions (auteur et destinataire) d'un user
	 */
	public function getDiscussions($user)
	{
		return $this->createQueryBuilder('d')
			->where('d.auteur = :user or d.destinataire = :user')
			->setParameter('user', $user)
			->orderBy('d.date', 'DESC')
			->getQuery()
			->getResult()
		;
	}

	/**
	 * @return Renvoie les discussions (auteur et destinataire) d'un user
	 */
	public function getDiscussionsExceptAdmins()
	{
		return $this->createQueryBuilder('d')
			->where(' d.destinataire is not null')
			->orderBy('d.date', 'DESC')
			->getQuery()
			->getResult()
		;
	}
}
