<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__ . '/app')
    ->in(__DIR__ . '/routes')
    ->in(__DIR__ . '/tests');

return (new Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'new_with_parentheses' => true,
        'unary_operator_spaces' => true,
        'not_operator_with_successor_space' => true,
        'no_unused_imports' => true,

    ])

    ->setFinder($finder)
    ->setRiskyAllowed(true);
