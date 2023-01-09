<?php

namespace App\Repository;

use App\Entity\SeanceLieu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SeanceLieu>
 *
 * @method SeanceLieu|null find($id, $lockMode = null, $lockVersion = null)
 * @method SeanceLieu|null findOneBy(array $criteria, array $orderBy = null)
 * @method SeanceLieu[]    findAll()
 * @method SeanceLieu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeanceLieuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeanceLieu::class);
    }

    public function add(SeanceLieu $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SeanceLieu $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
