<?php

namespace App\Command;

use App\Repository\TaxRuleRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Cache\CacheInterface;

#[AsCommand(
    name: 'cache:warm-tax',
    description: 'Warm up the cache of tax rules',
)]
class CacheWarmTaxCommand extends Command
{
    public function __construct(
        private TaxRuleRepository $taxRuleRepository,
        private CacheInterface $cache
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->taxRuleRepository->findCacheTaxRule($this->cache);

        $io->success('The cache of tax rules is warmed up!');

        return Command::SUCCESS;
    }
}
