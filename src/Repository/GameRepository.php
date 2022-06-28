<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Game $entity, bool $flush = true): void
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
    public function remove(Game $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Renvoie tout les jeux en array
     */
    public function allArray()
    {
        return $this->createQueryBuilder('x')
            ->join('x.owner', 'u')
            ->select(
                'x.id',
                'x.name',
                'x.nbPlayers',
                'x.difficult',
                'x.version',
                'x.minAge',
                'x.time',
                'u.id as user_id',
                'u.userName',
                'u.nom',
                'u.prenom',
            )
            ->orderBy('x.name', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
