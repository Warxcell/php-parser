<?php

namespace VM5\PhpParser\DocBlockVisitor;

use phpDocumentor\Reflection\DocBlock;
use VM5\PhpParser\DocBlockVisitor;

class CommentsRemoverDocBlockVisitor implements DocBlockVisitor
{
    public function visit(DocBlock $docBlock)
    {
        $newTags = [];
        $excluded = ['author', 'copyright'];
        foreach ($docBlock->getTags() as $tag) {
            if (!in_array($tag->getName(), $excluded)) {
                $newTags[] = $tag;
            }
        }

        if (count($newTags) != 0) {
            return new DocBlock(
                $docBlock->getSummary(),
                $docBlock->getDescription(),
                $newTags,
                $docBlock->getContext(),
                $docBlock->getLocation(),
                $docBlock->isTemplateStart(),
                $docBlock->isTemplateEnd()
            );
        }
    }

}