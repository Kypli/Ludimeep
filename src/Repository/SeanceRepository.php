<?php

namespace App\Repository;

use App\Entity\Seance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Seance>
 *
 * @method Seance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seance[]    findAll()
 * @method Seance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeanceRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Seance::class);
	}

	public function add(Seance $entity, bool $flush = false): void
	{
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Seance $entity, bool $flush = false): void
	{
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function getDateRunning()
	{
		return $this->createQueryBuilder('x')

			->where('x.date > :now')
			->setParameter(':now', new \Datetime('now'))

			->orderBy('x.date', 'ASC')

			->getQuery()
			->getResult()
		;
	}

	public function getDateOver()
	{
		return $this->createQueryBuilder('x')

			->where('x.date <= :now')
			->setParameter(':now', new \Datetime('now'))

			->orderBy('x.date', 'DESC')

			->getQuery()
			->getResult()
		;
	}

	public function getOldSeance($number)
	{
		return $this->createQueryBuilder('x')

			->where('x.date <= :now')
			->setParameter(':now', new \Datetime('now'))

			->orderBy('x.date', 'DESC')
			->setMaxResults($number)
			
			->getQuery()
			->getResult()
		;
	}

	public function getNextSeance($number)
	{
		return $this->createQueryBuilder('x')

			->where('x.date > :now')
			->setParameter(':now', new \Datetime('now'))

			->orderBy('x.date', 'ASC')
			->setMaxResults($number)

			->getQuery()
			->getResult()
		;
	}

	public function getOneSeanceByDate($date)
	{
		$date_start = new \Datetime($date." 00:00:00");
		$date_end = new \Datetime($date." 23:59:59");

		return $this->createQueryBuilder('x')

			->select('x')

			->where('x.date >= :date_start')
			->setParameter(':date_start', $date_start)

			->andWhere('x.date <= :date_end')
			->setParameter(':date_end', $date_end)

			->orderBy('x.date', 'ASC')
			->setMaxResults(1)

			->getQuery()
			->getResult()
		;
	}
}
