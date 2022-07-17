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

    public function countSondageRunning($sondage_id = 0): int
    {

        return $this->createQueryBuilder('x')
            ->select('COUNT(x)')

            ->where('x.start <= :now')
            ->andWhere('x.end > :now')
            ->setParameter(':now', new \Datetime('now'))

            ->andWhere('x.id != :sondage_id')
            ->setParameter(':sondage_id', $sondage_id)

            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getSondageRunning(): Array
    {
        return $this->createQueryBuilder('x')
            ->select('x')
            ->where('x.start <= :now')
            ->andWhere('x.end > :now')
            ->setParameter(':now', new \Datetime('now'))
            ->getQuery()
            ->getResult()
        ;
    }
}
