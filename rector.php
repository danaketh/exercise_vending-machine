<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictScalarReturnExprRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths(
        [
            __DIR__.'/public',
            __DIR__.'/src',
            __DIR__.'/tests',
        ]
    );

    $rectorConfig->sets(
        [
            SetList::CODE_QUALITY,
            SetList::DEAD_CODE,
            SetList::CODING_STYLE,
            SetList::NAMING,
            SetList::TYPE_DECLARATION,
            SetList::PRIVATIZATION,
            SetList::EARLY_RETURN,
            SetList::TYPE_DECLARATION,
            SetList::INSTANCEOF,
            SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        ]
    );

    $rectorConfig->rules(
        [
            ReturnTypeFromStrictNativeCallRector::class,
            ReturnTypeFromStrictScalarReturnExprRector::class,
        ]
    );
};
