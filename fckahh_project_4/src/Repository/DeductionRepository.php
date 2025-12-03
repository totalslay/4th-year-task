<?php

namespace App\Repository;

use App\Entity\Deduction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Deduction>
 */
class DeductionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deduction::class);
    }

    public function findDeductions(string $type, int $periodId): array
    {
        return $this->createQueryBuilder('d')
            ->select('e.fullName', 'd.amount', 'd.type')
            ->join('d.employee', 'e')
            ->andWhere('d.type = :type')
            ->andWhere('d.period = :periodId')
            ->setParameter('type', $type)
            ->setParameter('periodId', $periodId)
            ->orderBy('d.amount', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findTotalDeductionsByEmployee(int $employeeId, int $periodId): float
    {
        $result = $this->createQueryBuilder('d')
            ->select('SUM(d.amount) as total')
            ->andWhere('d.employee = :employeeId')
            ->andWhere('d.period = :periodId')
            ->setParameter('employeeId', $employeeId)
            ->setParameter('periodId', $periodId)
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? (float) $result : 0.0;
    }
}
