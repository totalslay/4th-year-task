<?php

namespace App\Repository;

use App\Entity\Accrual;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Accrual>
 */
class AccrualRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accrual::class);
    }

    public function findOvertimeByPeriod(int $periodId): array
    {
        return $this->createQueryBuilder('a')
            ->select('e.fullName', 'a.amount', 'a.type')
            ->join('a.employee', 'e')
            ->andWhere('a.period = :periodId')
            ->andWhere('a.type LIKE :type')
            ->setParameter('periodId', $periodId)
            ->setParameter('type', '%OVERTIME%')
            ->orderBy('a.amount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findEmployeesOvertime(int $periodId): array
    {
        return $this->createQueryBuilder('a')
            ->select('e.id', 'e.fullName', 'SUM(a.amount) as totalOvertime')
            ->join('a.employee', 'e')
            ->andWhere('a.period = :periodId')
            ->andWhere('a.type = :type')
            ->setParameter('periodId', $periodId)
            ->setParameter('type', 'OVERTIME')
            ->groupBy('e.id')
            ->having('SUM(a.amount) > 20')
            ->orderBy('totalOvertime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByTypeAndPeriod(string $type, int $periodId): array
    {
        return $this->createQueryBuilder('a')
            ->select('e.fullName', 'a.amount', 'a.type')
            ->join('a.employee', 'e')
            ->andWhere('a.type = :type')
            ->andWhere('a.period = :periodId')
            ->setParameter('type', $type)
            ->setParameter('periodId', $periodId)
            ->orderBy('a.amount', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
