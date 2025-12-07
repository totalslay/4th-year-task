<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('node_modules')
    ->exclude('public')
    ->exclude('tests');

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PHP80Migration' => true,
        '@PHP80Migration:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => false,
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_order' => true,
        'yoda_style' => false,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/var/.php_cs.cache')
    ->setRiskyAllowed(true);