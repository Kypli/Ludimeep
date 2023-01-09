<?php

namespace App\Repository;

use App\Entity\UserProfil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserProfil>
 *
 * @method UserProfil|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProfil|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProfil[]    findAll()
 * @method UserProfil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserProfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserProfil::class);
    }

    public function add(UserProfil $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserProfil $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
