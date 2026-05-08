<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

return new PhpCsFixer\Config()
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'declare_strict_types' => ['preserve_existing_declaration' => true],
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
        'phpdoc_align' => false,
        'phpdoc_to_comment' => false, // Keep PHPStan type annotations
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setCacheFile('tools/php-cs-fixer/.php-cs-fixer.cache')
;
