<?php

/**
 * @see https://cs.symfony.com/doc/rules/index.html
 */
$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
    ->exclude('kernel')
;

return (new PhpCsFixer\Config('typesense-bundle'))
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'none'],
        'phpdoc_align' => ['align' => 'vertical'],
        'yoda_style' => false, // Disable Yoda conditions for readability
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'single_line_throw' => false,
    ])
    ->setLineEnding(PHP_EOL)
    ->setFinder($finder)
;
