<?php

namespace VM5\PhpParser;

use phpDocumentor\Reflection\DocBlock;

interface DocBlockVisitor
{
    /**
     * @param DocBlock $docBlock
     * @return DocBlock|null
     */
    public function visit(DocBlock $docBlock);
}