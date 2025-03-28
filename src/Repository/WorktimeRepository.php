<?php

namespace App\Repository;

use App\Entity\Worktime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Employee;

/**
 * @extends ServiceEntityRepository<Worktime>
 */
class WorktimeRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Worktime::class);
  }

  public function findByMonth(
    int $year,
    int $month,
    Employee $employee,
  ) {
    $startDate = new \DateTime("$year-$month-01");
    $endDate = clone $startDate;
    $endDate->modify('last day of this month');
    $endDate->setTime(23, 59, 59);

    return $this->createQueryBuilder('wt')
      ->where('wt.startDay >= :startDate')
      ->andWhere('wt.startDay <= :endDate')
      ->andWhere('wt.employee = :employee')
      ->setParameter('startDate', $startDate->format('Y-m-d'))
      ->setParameter('endDate', $endDate->format('Y-m-d'))
      ->setParameter('employee', $employee)
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
