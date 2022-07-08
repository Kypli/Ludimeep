<?php

namespace App\Repository;

use App\Entity\Sondage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sondage>
 *
 * @method Sondage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sondage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sondage[]    findAll()
 * @method Sondage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SondageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sondage::class);
    }

    public function add(Sondage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sondage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countSondageRunning(): int
    {
        return $this->createQueryBuilder('x')
            ->select('COUNT(x)')
            ->where('x.start <= :now')
            ->andWhere('x.end > :now')
            ->setParameter(':now', new \Datetime('now'))
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getVotantsBySondageId($id): Array
    {
        return $this->createQueryBuilder('x')
            ->select([
                'SUM(x.result1)',
                'SUM(x.result2)',
                'SUM(x.result3)',
                'SUM(x.result4)',
                'SUM(x.result5)',
                'SUM(x.result6)',
                'SUM(x.result7)',
                'SUM(x.result8)',
            ])
            ->where('x.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getScalarResult()
        ;
    }
}
