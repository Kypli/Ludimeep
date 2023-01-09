<?php

namespace App\Repository;

use App\Entity\SeanceType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SeanceType>
 *
 * @method SeanceType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SeanceType|null findOneBy(array $criteria, array $orderBy = null)
 * @method SeanceType[]    findAll()
 * @method SeanceType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeanceTypeRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, SeanceType::class);
	}

	public function add(SeanceType $entity, bool $flush = false): void
	{
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(SeanceType $entity, bool $flush = false): void
	{
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}
}
