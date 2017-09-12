<?php

namespace VM5\PhpParser\DocBlockVisitor;

use VM5\PhpParser\DocBlockVisitor;

class ConvertSimpleOrm implements DocBlockVisitor
{

    private function convertToOrm(\phpDocumentor\Reflection\DocBlock\Tag $tag)
    {
        return 'Doctrine\ORM\Mapping\\'.$tag->getName();
    }

    private function isOrmTag(\phpDocumentor\Reflection\DocBlock\Tag $tag)
    {
        return class_exists($this->convertToOrm($tag));
    }

    public function visit(\phpDocumentor\Reflection\DocBlock $docBlock)
    {
        $newTags = $docBlock->getTags();

        foreach ($docBlock->getTags() as $i => $tag) {
            if ($tag instanceof \phpDocumentor\Reflection\DocBlock\Tags\Generic && $this->isOrmTag($tag)) {
                $description = $tag->getDescription();

//                if ($description) {
//
//                }

                $newTags[$i] = new \phpDocumentor\Reflection\DocBlock\Tags\Generic(
                    $this->convertToOrm($tag), $description
                );
            }
        }

        return new \phpDocumentor\Reflection\DocBlock(
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