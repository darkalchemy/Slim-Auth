<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor', 'node_modules', 'resources/views/cache', 'public/resources', 'var', 'locale'])
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new PhpCsFixer\Config();

return $config
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12'                                 => true,
        '@PhpCsFixer'                            => true,
        'binary_operator_spaces'                 => ['operators' => ['=' => 'align_single_space', '=>' => 'align_single_space']],
        'combine_consecutive_unsets'             => true,
        'concat_space'                           => ['spacing' => 'one'],
        'no_superfluous_phpdoc_tags'             => false,
        'ternary_to_null_coalescing'             => true,
        'method_argument_space'                  => ['on_multiline' => 'ensure_fully_multiline'],
        'declare_equal_normalize'                => ['space' => 'none'],
        'declare_strict_types'                   => true,
        'yoda_style'                             => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    ])
    ->setFinder($finder);
