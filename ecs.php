<?php

use Symplify\CodingStandard\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer;
use Symplify\CodingStandard\Fixer\Spacing\StandaloneLinePromotedPropertyFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;

return static function (ECSConfig $ecsConfig): void {

    $ecsConfig->sets([
        SetList::SPACES,
    ]);

    $ecsConfig->ruleWithConfiguration(LineLengthFixer::class, [
        LineLengthFixer::LINE_LENGTH => 80,
    ]);

    $ecsConfig->rules([
        StandaloneLineInMultilineArrayFixer::class,
        StandaloneLinePromotedPropertyFixer::class
    ]);
};
