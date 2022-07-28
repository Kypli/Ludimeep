<?php

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Photo>
 *
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Photo::class);
	}

	/**
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function add(Photo $entity, bool $flush = true): void
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
	public function remove(Photo $entity, bool $flush = true): void
	{
		$this->_em->remove($entity);
		if ($flush) {
			$this->_em->flush();
		}
	}

	/**
	 * @return User[] Returns an array of User objects
	 */
	public function getPhotos($user_id, $admin, $idLastImage, $maxResult)
	{		
		$q = $this->createQueryBuilder('x');

		if (!$admin){

			// PHOTOS VALIDES
			$q->where('x.valid = true');

			// USER EXISTANT
			if ($user_id != 0){
				$q
					// LANCEUR D'ALERTES
					->orWhere('x.lanceurAlerte is not null and x.lanceurAlerte = :user_id')
					->setParameter('user_id', $user_id)
			
					// PROPRIETAIRE
					->orWhere('x.user = :user_id')
					->setParameter('user_id', $user_id)
				;
			}
		}

		// FILTRE START
		if ($idLastImage != null){
			$q
				->andWhere('x.id < :idLastImage')
				->setParameter('idLastImage', $idLastImage)
			;
		}

		return $q
			->setMaxResults($maxResult)
			->orderBy('x.id', 'DESC')
			->getQuery()
			->getResult()
		;
	}

	/**
	 * @return User[] Returns an array of User objects
	 */
	public function getPhotosName()
	{
		return $this->createQueryBuilder('p')
			->select('p.name')
			->getQuery()
			->getArrayResult()
		;
	}
}
