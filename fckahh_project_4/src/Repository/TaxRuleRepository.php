<?php

namespace App\Repository;

use App\Entity\TaxRule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @extends ServiceEntityRepository<TaxRule>
 */
class TaxRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaxRule::class);
    }

    public function FindCacheTaxRule(CacheInterface $cache): array
    {
        return $cache->get('tax_rules_cache', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            return $this->createQueryBuilder('t')
                ->andWhere('t.taxType = :taxType')
                ->setParameter('taxType', 'INCOME_TAX')
                ->orderBy('t.minAmount', 'ASC')
                ->getQuery()
                ->getResult();
        });
    }

    public function findProgressiveTaxRules(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.taxType = :taxType')
            ->setParameter('taxType', 'INCOME_TAX')
            ->orderBy('t.minAmount', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function invalidateTaxCache(CacheInterface $cache): void
    {
        $cache->delete('tax_rules_cache');
    }
}
