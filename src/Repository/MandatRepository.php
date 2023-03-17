<?php

namespace App\Repository;

use App\Entity\Mandat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mandat>
 *
 * @method Mandat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mandat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mandat[]    findAll()
 * @method Mandat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MandatRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Mandat::class);
	}

	public function add(Mandat $entity, bool $flush = false): void
	{
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Mandat $entity, bool $flush = false): void
	{
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}
}
