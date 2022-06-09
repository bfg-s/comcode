<?php

$config = new PhpCsFixer\Config();

return $config
    ->setRules([

        '@PSR12' => true,
        '@PHP81Migration' => true,
        '@Symfony' => true,
        'use_arrow_functions' =>true,

        'class_attributes_separation' => [
            'elements' => ['const' => 'one', 'method' => 'one', 'property' => 'one', 'trait_import' => 'none', 'case' => 'none']
        ],
        'array_syntax' => ['syntax' => 'short'],
        'array_push' => true,
        'modernize_strpos' => true,
        'no_alias_language_construct_call' => true,
        'no_whitespace_before_comma_in_array' => true,
        'normalize_index_brace' => true,
        'trim_array_spaces' => true,
        'constant_case' => ['case' => 'lower'],
        'lowercase_keywords' => true,
        'lowercase_static_reference' => true,
        'magic_constant_casing' => true,
        'magic_method_casing' => true,
        'native_function_casing' => true,
        'native_function_type_declaration_casing' => true,
        'lowercase_cast' => true,
        'short_scalar_cast' => true,
//        'ordered_class_elements' => [
//            'sort_algorithm' => 'alpha',
//            'order' => ['use_trait', 'public', 'protected', 'private', 'case', 'constant', 'constant_public', 'constant_protected', 'constant_private', 'property', 'property_static', 'property_public', 'property_protected', 'property_private', 'property_public_readonly', 'property_protected_readonly', 'property_private_readonly', 'property_public_static', 'property_protected_static', 'property_private_static', 'method', 'method_abstract', 'method_static', 'method_public', 'method_protected', 'method_private', 'method_public_abstract', 'method_protected_abstract', 'method_private_abstract', 'method_public_abstract_static', 'method_protected_abstract_static', 'method_private_abstract_static', 'method_public_static', 'method_protected_static', 'method_private_static', 'construct', 'destruct', 'magic', 'phpunit']
//        ],
        'single_trait_insert_per_statement' => true,
        'control_structure_continuation_position' => true,
        'no_alternative_syntax' => true,
        'no_useless_else' => true,
        'simplified_if_return' => true,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'return_type_declaration' => ['space_before' => 'none'],
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'single_import_per_statement' => true,
        'single_line_after_imports' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'dir_constant' => true,
        'list_syntax' => true,
        'blank_line_after_namespace' => true,
        'no_leading_namespace_whitespace' => true,
        'single_blank_line_before_namespace' => true,
        'concat_space' => ['spacing' => 'one'],
        'no_space_around_double_colon' => true,
        'not_operator_with_successor_space' => true,
        'object_operator_without_whitespace' => true,
        'operator_linebreak' => ['position' => 'beginning'],
        'ternary_operator_spaces' => true,
        'ternary_to_null_coalescing' => true,
        'unary_operator_spaces' => true,
        'no_closing_tag' => true,
        'no_useless_return' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'array_indentation' => true,
        'blank_line_before_statement' => ['statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try']],
        'compact_nullable_typehint' => true,
        'method_chaining_indentation' => true,
        'no_spaces_around_offset' => true,
        'no_spaces_inside_parenthesis' => true,
        'no_whitespace_in_blank_line' => true,
        'types_spaces' => ['space' => 'none'],
        'new_with_braces' => false,



        //'@PSR12' => true,
//        '@PHP81Migration' => true,
//        '@PhpCsFixer' => true,
//
//
//        'no_leading_import_slash' => true,
//        'no_unused_imports' => true,
//
//
//        'new_with_braces' => false,
//        'array_indentation' => true,
//        'array_syntax' => ['syntax' => 'short'],
//        'combine_consecutive_unsets' => true,
//        'multiline_whitespace_before_semicolons' => true,
//        'single_quote' => true,
//        'blank_line_before_statement' => true,
//        'braces' => [
//            'allow_single_line_closure' => true,
//        ],
//        'concat_space' => ['spacing' => 'one'],
//        'declare_equal_normalize' => true,
//        'function_typehint_space' => true,
//        'include' => true,
//        'lowercase_cast' => true,
//        'no_multiline_whitespace_around_double_arrow' => true,
//        'no_spaces_around_offset' => true,
//        'no_whitespace_before_comma_in_array' => true,
//        'no_whitespace_in_blank_line' => true,
//        'object_operator_without_whitespace' => true,
//        'single_blank_line_before_namespace' => true,
//        'ternary_operator_spaces' => true,
//        'trailing_comma_in_multiline' => true,
//        'trim_array_spaces' => true,
//        'unary_operator_spaces' => true,
//        'binary_operator_spaces' => true,
//        'whitespace_after_comma_in_array' => true,
//        'single_trait_insert_per_statement' => true,
//
//
//        'simplified_if_return' => true,
//        'use_arrow_functions' => true,
//        'fully_qualified_strict_types' => true,
//        'return_type_declaration' => true,
//        'get_class_to_class_keyword' => true,
//        'explicit_indirect_variable' => true,
//
//
//        'function_to_constant' => false,
//        'return_assignment' => false,
    ])
    ->setLineEnding("\n");
