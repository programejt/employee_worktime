<?php

namespace App\Repository;

use App\Entity\Worktime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Worktime>
 */
class WorktimeRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Worktime::class);
  }

  public function findByMonth(int $year, int $month)
  {
    return $this->createQueryBuilder('wt')
      ->where('YEAR(wt.startDay) = :year')
      ->andWhere('MONTH(wt.startDay) = :month')
      ->setParameter('year', $year)
      ->setParameter('month', $month)
      ->getQuery()
      ->getResult();
  }

  //    /**
  //     * @return Worktime[] Returns an array of Worktime objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('w')
  //            ->andWhere('w.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('w.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?Worktime
  //    {
  //        return $this->createQueryBuilder('w')
  //            ->andWhere('w.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
