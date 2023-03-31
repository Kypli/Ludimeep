<?php

namespace App\Repository;

use App\Entity\Organigramme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Organigramme>
 *
 * @method Organigramme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organigramme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organigramme[]    findAll()
 * @method Organigramme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganigrammeRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Organigramme::class);
	}

	public function add(Organigramme $entity, bool $flush = false): void
	{
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Organigramme $entity, bool $flush = false): void
	{
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	/**
	 * Clean les orga inactif sans user
	 */
	public function cleanUseless()
	{
		$q = $this->createQueryBuilder('x')
			->where('x.isActif = FALSE and x.user is NULL')
			->getQuery()
			->getResult()
		;

		foreach($q as $entity){
			$this->getEntityManager()->remove($entity);
			$this->getEntityManager()->flush();
		}

		return $q;
	}
}
