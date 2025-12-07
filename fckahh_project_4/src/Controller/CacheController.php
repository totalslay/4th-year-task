<?php

namespace App\Controller;

use App\Repository\TaxRuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;

final class CacheController extends AbstractController
{
    #[Route('/cache/tax', name: 'app_cache_tax')]
    public function taxCache(TaxRuleRepository $taxRuleRepository, CacheInterface $cache): Response
    {
        $cachedRules = $taxRuleRepository->FindCacheTaxRule($cache);

        return $this->render('cache/tax.html.twig', [
            'cached_rules' => $cachedRules,
            'cache_key' => 'tax_rules_cache',
        ]);
    }

    #[Route('/cache/tax/clear', name: 'app_cache_tax_clear')]
    public function clearTaxCache(CacheInterface $cache): Response
    {
        $cache->delete('tax_rules_cache');
        $this->addFlash('success', 'Tax rule cache cleared');

        return $this->redirectToRoute('app_cache_tax');
    }

    #[Route('/cache/tax/warm', name: 'app_cache_tax_warm')]
    public function warmTaxCache(CacheInterface $cache, TaxRuleRepository $taxRuleRepository): Response
    {
        $taxRuleRepository->findCacheTaxRule($cache);

        $this->addFlash('success', 'Cache warmed up');

        return $this->redirectToRoute('app_cache_tax');
    }
}
