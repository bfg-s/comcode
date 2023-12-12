<?php

$config = new PhpCsFixer\Config();

return $config
    ->registerCustomFixers(new PhpCsFixerCustomFixers\Fixers())
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
    ->setRules([
        'PedroTroller/line_break_between_method_arguments' => [ 'max-args' => 4, 'max-length' => 120, 'automatic-argument-merge' => true ],
        'PedroTroller/phpspec' => [ 'instanceof' => [ 'PhpSpec\ObjectBehavior' ] ],
    ])
    ->setRules([
        \PhpCsFixerCustomFixers\Fixer\MultilinePromotedPropertiesFixer::name() => true,
        \PhpCsFixerCustomFixers\Fixer\ConstructorEmptyBracesFixer::name() => true,
        \PhpCsFixerCustomFixers\Fixer\NoDuplicatedArrayKeyFixer::name() => true,
        \PhpCsFixerCustomFixers\Fixer\NoDuplicatedImportsFixer::name() => true,
        '@PSR12' => true,
        '@PHP81Migration' => true,
        '@PhpCsFixer' => true,
        'array_push' => true,
        'array_syntax' => ['syntax' => 'short'],
        'braces' => [
            'position_after_functions_and_oop_constructs' => 'next',
            'position_after_control_structures' => 'same',
            'position_after_anonymous_constructs' => 'same',
        ],
        'constant_case' => ['case' => 'lower'],
        'lowercase_keywords' => true,
        'lowercase_static_reference' => true,
        'magic_constant_casing' => true,
        'magic_method_casing' => true,
        'native_function_casing' => true,
        'native_function_type_declaration_casing' => true,
        'cast_spaces' => ['space' => 'single'],
        'lowercase_cast' => true,
        'no_short_bool_cast' => true,
        'short_scalar_cast' => true,
        'class_attributes_separation' => [
            'elements' => ['const' => 'one', 'method' => 'one', 'property' => 'one', 'trait_import' => 'none', 'case' => 'none']
        ],
        'no_blank_lines_after_class_opening' => true,
        'single_class_element_per_statement' => true,
        'single_trait_insert_per_statement' => true,
        'visibility_required' => [
            'elements' => ['property', 'method', 'const']
        ],
        'control_structure_continuation_position' => [
            'position' => 'same_line'
        ],
        'no_alternative_syntax' => [
            'fix_non_monolithic_code' => true
        ],
        'no_unneeded_control_parentheses' => [
            'statements' => ['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case', 'yield', 'yield_from']
        ],
        'no_unneeded_curly_braces' => [
            'namespaces' => true
        ],
        'no_useless_else' => true,
        'simplified_if_return' => true,
        'switch_case_semicolon_to_colon' => true,
        'switch_case_space' => true,
        'switch_continue_to_break' => true,
        'trailing_comma_in_multiline' => true,
        'function_declaration' => [
            'closure_function_spacing' => 'one',
        ],
        'function_typehint_space' => true,
        'lambda_not_used_import' => true,
        'method_argument_space' => [
            'keep_multiple_spaces_after_comma' => false,
            'on_multiline' => 'ensure_fully_multiline'
        ],
        'nullable_type_declaration_for_default_null_value' => true,
        'return_type_declaration' => [
            'space_before' => 'none'
        ],
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => [
            'import_constants' => true,
            'import_functions' => true,
            'import_classes' => true,
        ],
        'ordered_imports' => [
            'sort_algorithm' => 'none', 'imports_order' => ['const', 'class', 'function']
        ],
        'single_import_per_statement' => true,
        'single_line_after_imports' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'declare_equal_normalize' => [
            'space' => 'single'
        ],
        'explicit_indirect_variable' => true,
        'single_space_after_construct' => true,
        'list_syntax' => [
            'syntax' => 'short'
        ],
        'blank_line_after_namespace' => true,
        'no_leading_namespace_whitespace' => true,
        'assign_null_coalescing_to_coalesce_equal' => true,
        'binary_operator_spaces' => true,
        'concat_space' => ['spacing' => 'one'],
        'not_operator_with_successor_space' => true,
        'object_operator_without_whitespace' => true,
        'operator_linebreak' => true,
        'ternary_operator_spaces' => true,
        'ternary_to_null_coalescing' => true,
        'unary_operator_spaces' => true,
        'full_opening_tag' => true,
        'no_closing_tag' => true,
        'align_multiline_comment' => true,
        'phpdoc_no_empty_return' => false,
        'phpdoc_no_useless_inheritdoc' => false,
        'phpdoc_separation' => false,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_align' => [
            'align' => 'left'
        ],
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_indent' => true,
        'phpdoc_line_span' => true,
        'phpdoc_order' => true,
        'phpdoc_return_self_reference' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'phpdoc_types_order' => true,
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_var_without_name' => true,
        'no_useless_return' => true,
        'return_assignment' => false,
        'simplified_null_return' => true,
        'multiline_whitespace_before_semicolons' => true,
        'no_empty_statement' => true,
        'no_superfluous_phpdoc_tags' => false,
        'no_singleline_whitespace_before_semicolons' => true,
        'single_quote' => [
            'strings_containing_single_quote_chars' => true
        ],
        'array_indentation' => true,
        'blank_line_before_statement' => [
            'statements' => ['break', 'case', 'continue', 'declare', 'default', 'phpdoc', 'do', 'exit', 'for', 'foreach', 'goto', 'if', 'include', 'include_once', 'require', 'require_once', 'return', 'switch', 'throw', 'try', 'while', 'yield', 'yield_from']
        ],
        'compact_nullable_typehint' => true,
        'no_extra_blank_lines' => [
            'tokens' => ['break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use', 'use_trait']
        ],
        'no_spaces_around_offset' => true,
        'no_spaces_inside_parenthesis' => true,
        'no_whitespace_in_blank_line' => true,
        'types_spaces' => true,
        'ordered_class_elements' => true,
        'php_unit_method_casing' => [
            'case' => 'snake_case'
        ],
        'yoda_style' => [
            'equal' => false,
            'identical' => false
        ],
        'new_with_braces' => false,
        'use_arrow_functions' => true,
        'php_unit_test_class_requires_covers' => false,

        'final_internal_class' => false,
        'php_unit_internal_class' => false,
        'php_unit_dedicate_assert_internal_type' => false,
    ])
    ->setLineEnding("\n");
