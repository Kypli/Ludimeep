<?php

namespace App\Repository;

use App\Entity\Message;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Message::class);
	}

	/**
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function add(Message $entity, bool $flush = true): void
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
	public function remove(Message $entity, bool $flush = true): void
	{
		$this->_em->remove($entity);
		if ($flush) {
			$this->_em->flush();
		}
	}

	/**
	 * @return Renvoie le nombre de message non lu d'un user
	 */
	public function messageNonLue($user)
	{
		return $this->createQueryBuilder('m')
			->leftJoin('m.discussion', 'd')
			->where('d.auteur = :user or d.destinataire = :user ')
			->andWhere('m.user != :user')
			->andWhere('m.lu = false')
			->setParameter('user', $user)
			->select('COUNT(m.id)')
			->getQuery()
			->getSingleScalarResult()
		;
	}

	/**
	 * @return Renvoie le nombre de message admin non lu
	 */
	public function messageAdminNonLue($user)
	{
		return $this->createQueryBuilder('m')
			->leftJoin('m.discussion', 'd')
			->where('d.destinataire is null')
			->andWhere('m.user != :user')
			->setParameter('user', $user)
			->andWhere('m.lu = false')
			->select('COUNT(m.id)')
			->getQuery()
			->getSingleScalarResult()
		;
	}

	/**
	 * @return Renvoie le nombre de message dans une discussion
	 */
	public function countMessagesInDiscussion($discussion)
	{
		return $this->createQueryBuilder('m')
			->where('m.discussion = :discussion')
			->setParameter('discussion', $discussion)
			->select('COUNT(m)')
			->getQuery()
			->getSingleScalarResult()
		;
	}

	/**
	 * @return Renvoie le nombre message non lu d'une discussion
	 */
	public function getMessagesNonLu($discussion, $user)
	{
		return $this->createQueryBuilder('m')
			->leftJoin('m.user', 'u')
			->where('m.discussion = :discussion')
			->setParameter('discussion', $discussion)
			->andWhere('m.user != :user')
			->setParameter('user', $user)
			->andWhere('m.lu = false')
			->select('COUNT(m)')
			->getQuery()
			->getSingleScalarResult()
		;
	}

	/**
	 * @return Renvoie le nombre message non lu d'une discussion pour les admins
	 */
	public function getMessagesNonLuAdmin($discussion)
	{
		return $this->createQueryBuilder('m')
			->leftJoin('m.discussion', 'd')
			->where('m.discussion = :discussion')
			->setParameter('discussion', $discussion)
			->andWhere('d.auteur = m.user')
			->andWhere('m.lu = false')
			->select('COUNT(m)')
			->getQuery()
			->getSingleScalarResult()
		;
	}
}
