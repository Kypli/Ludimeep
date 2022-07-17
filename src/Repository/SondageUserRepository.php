<?php

namespace App\Repository;

use App\Entity\SondageUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SondageUser>
 *
 * @method SondageUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method SondageUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method SondageUser[]    findAll()
 * @method SondageUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SondageUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SondageUser::class);
    }

    public function add(SondageUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SondageUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getVotantsBySondageId($id): String
    {
        return $this->createQueryBuilder('x')
            ->select('COUNT(x)')
            ->where('x.sondage = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function myVote($sondage_id, $user_id): String
    {
        return $this->createQueryBuilder('x')
            ->select('x.vote')
            ->where('x.sondage = :sondage_id')
            ->setParameter(':sondage_id', $sondage_id)
            ->andWhere('x.votant = :user_id')
            ->setParameter(':user_id', $user_id)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
