<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

$header = <<<'EOF'
This file is part of PHP CS Fixer.
(c) Fabien Potencier <fabien@symfony.com>
    Dariusz Rumiński <dariusz.ruminski@gmail.com>
This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR2'                                      => true,
        'array_syntax'                               => ['syntax' => 'short'],
        'no_extra_consecutive_blank_lines'           => ['break', 'continue', 'extra', 'return', 'throw', 'use'], // 'parenthesis_brace_block', 'square_brace_block', 'curly_brace_block'
        'no_useless_else'                            => true,
        'no_useless_return'                          => true,
        'ordered_class_elements'                     => true,
        'phpdoc_add_missing_param_annotation'        => true,
        'function_typehint_space'                    => true,
        'single_quote'                               => true,
        'no_blank_lines_before_namespace'            => true,
        'encoding'                                   => true,
        'elseif'                                     => true,
        'function_declaration'                       => true,
        'lowercase_constants'                        => true,
        'lowercase_keywords'                         => true,
        'method_argument_space'                      => true,
        'no_trailing_whitespace_in_comment'          => true,
        'single_line_after_imports'                  => true,
        'switch_case_semicolon_to_colon'             => true,
        'switch_case_space'                          => true,
        'no_blank_lines_after_class_opening'         => true,
        'native_function_casing'                     => true,
        'class_definition'                           => true,
        'braces'                                     => true,
        'no_whitespace_before_comma_in_array'        => true,
        'whitespace_after_comma_in_array'            => true,
        'concat_space'                               => ['spacing' => 'none'],
        'single_blank_line_at_eof'                   => true,
        'no_spaces_after_function_name'              => true,
        'indentation_type'                           => true,
        'blank_line_after_namespace'                 => true,
        'trailing_comma_in_multiline_array'          => true,
        'no_multiline_whitespace_before_semicolons'  => true,
        'single_import_per_statement'                => true,
        'no_leading_namespace_whitespace'            => true,
        'no_spaces_inside_parenthesis'               => true,
        'no_closing_tag'                             => true,
        'no_empty_comment'                           => true,
        'no_empty_phpdoc'                            => true,
        'no_empty_statement'                         => true,
        'no_leading_import_slash'                    => true,
        'blank_line_before_return'                   => true,
        'no_short_echo_tag'                          => true,
        'full_opening_tag'                           => true,
        'no_trailing_comma_in_singleline_array'      => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'ternary_operator_spaces'                    => true,
        'no_trailing_whitespace'                     => true,
        'no_unneeded_control_parentheses'            => true,
        'visibility_required'                        => true,
        'no_whitespace_in_blank_line'                => true,
        'linebreak_after_opening_tag'                => true,
        'native_function_casing'                     => true,
        'binary_operator_spaces'                     => [
            'align_double_arrow' => true,
            'align_equals'       => true,
        ],
        //        'header_comment'                      => ['header' => $header],
        //        'php_unit_strict' => true,
        //        'strict_comparison' => true,
        //        'strict_param' => true,
        //'combine_consecutive_unsets'                 => true,
    ])->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('tests/Fixtures')
            ->in(__DIR__)
    );
