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
        'array_syntax'                           => ['syntax' => 'short'],
        'binary_operator_spaces'                 => ['operators' => ['=' => 'align_single_space', '=>' => 'align_single_space']],
        'cast_spaces'                            => true,
        'combine_consecutive_unsets'             => true,
        'concat_space'                           => ['spacing' => 'one'],
        'linebreak_after_opening_tag'            => true,
        'no_blank_lines_after_class_opening'     => true,
        'no_blank_lines_after_phpdoc'            => true,
        'no_extra_blank_lines'                   => true,
        'no_trailing_comma_in_singleline_array'  => true,
        'no_whitespace_in_blank_line'            => true,
        'no_spaces_around_offset'                => true,
        'no_unused_imports'                      => true,
        'no_useless_else'                        => true,
        'no_useless_return'                      => true,
        'no_whitespace_before_comma_in_array'    => true,
        'normalize_index_brace'                  => true,
        'phpdoc_indent'                          => true,
        'phpdoc_to_comment'                      => true,
        'phpdoc_trim'                            => true,
        'no_superfluous_phpdoc_tags'             => false,
        'single_quote'                           => true,
        'ternary_to_null_coalescing'             => true,
        'trailing_comma_in_multiline'            => true,
        'trim_array_spaces'                      => true,
        'method_argument_space'                  => ['on_multiline' => 'ensure_fully_multiline'],
        'no_break_comment'                       => false,
        'blank_line_before_statement'            => true,
        'declare_equal_normalize'                => ['space' => 'none'],
        'declare_strict_types'                   => true,
        'fully_qualified_strict_types'           => true,
        'blank_line_after_opening_tag'           => true,
        'yoda_style'                             => false,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    ])
    ->setFinder($finder);

