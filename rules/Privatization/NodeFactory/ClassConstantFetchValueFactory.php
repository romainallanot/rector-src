<?php

declare(strict_types=1);

namespace Rector\Privatization\NodeFactory;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use Rector\Core\PhpParser\Node\NodeFactory;
use Rector\Core\PhpParser\Node\Value\ValueResolver;
use Rector\Privatization\Reflection\ClassConstantsResolver;

final class ClassConstantFetchValueFactory
{
    public function __construct(
        private ValueResolver $valueResolver,
        private NodeFactory $nodeFactory,
        private ClassConstantsResolver $classConstantsResolver
    ) {
    }

    /**
     * @param class-string $classWithConstants
     */
    public function create(Expr $expr, string $classWithConstants): ?ClassConstFetch
    {
        $value = $this->valueResolver->getValue($expr);
        if ($value === null) {
            return null;
        }

        $constantNamesToValues = $this->classConstantsResolver->getClassConstantNamesToValues($classWithConstants);
        foreach ($constantNamesToValues as $constantName => $constantValue) {
            if ($constantValue !== $value) {
                continue;
            }

            return $this->nodeFactory->createClassConstFetch($classWithConstants, $constantName);
        }

        return null;
    }
}
