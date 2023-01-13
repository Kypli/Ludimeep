<?php

namespace App\Repository;

use App\Entity\Tchat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tchat>
 *
 * @method Tchat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tchat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tchat[]    findAll()
 * @method Tchat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TchatRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Tchat::class);
	}

	public function add(Tchat $entity, bool $flush = false): void
	{
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Tchat $entity, bool $flush = false): void
	{
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	/**
	 * Renvoie les 30 derniers tchat de moins d'un mois
	 */
	public function getLastTchats($dateLimitShow)
	{
		$dateLimitShow = new \Datetime('- '.$dateLimitShow);

		return $this->createQueryBuilder('x')
			->leftjoin('x.user', 'u')
			->leftjoin('u.profil', 'up')

			->select([
				'u.userName as login',
				'up.nom',
				'up.prenom',
				'x.content',
				'x.date',
			])

			->where('x.date > :dateLess1Month')
			->andWhere('x.active = :true')
			->andWhere('u.active = :true')

			->orWhere('u IS NULL and x.date > :dateLess1Month and x.active = :true')

			->setParameters([
				':dateLess1Month' => $dateLimitShow,
				':true' => true,
			])

			->setMaxResults(30)
			->orderBy('x.date', 'DESC')

			->getQuery()
			->getArrayResult()
		;
	}
}
