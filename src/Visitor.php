<?php

namespace VM5\PhpCommentsRemover;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\DocBlockFactory;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\NodeVisitor;

class Visitor implements NodeVisitor
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    /**
     * @var Serializer
     */
    private $docBlockSerializer;

    /**
     * Traverser constructor.
     * @param DocBlockFactory $docBlockFactory
     * @param Serializer $docBlockSerializer
     */
    public function __construct(
        DocBlockFactory $docBlockFactory,
        Serializer $docBlockSerializer
    ) {
        $this->docBlockFactory = $docBlockFactory;
        $this->docBlockSerializer = $docBlockSerializer;
    }


    public function beforeTraverse(array $nodes)
    {
    }

    public function enterNode(Node $node)
    {

    }

    public function leaveNode(Node $node)
    {
        $docComments = $node->getAttribute('comments');
        if ($docComments) {
            $newDocComments = [];
            foreach ($docComments as $docComment) {
                if ($docComment instanceof Doc) {
                    $da = $this->docBlockFactory->create($docComment->getText());

                    $newTags = [];

                    $excluded = ['author', 'copyright'];
                    foreach ($da->getTags() as $tag) {
                        if (!in_array($tag->getName(), $excluded)) {
                            $newTags[] = $tag;
                        }
                    }

                    if (count($newTags) != 0) {
                        $cloned = new DocBlock(
                            $da->getSummary(),
                            $da->getDescription(),
                            $newTags,
                            $da->getContext(),
                            $da->getLocation(),
                            $da->isTemplateStart(),
                            $da->isTemplateEnd()
                        );

                        $newDocCommentText = $this->docBlockSerializer->getDocComment($cloned);

                        $class = get_class($docComment);
                        $newDocComment = new $class($newDocCommentText);
                        $newDocComments[] = $newDocComment;
                    }
                }
            }
            $node->setAttribute('comments', $newDocComments);
        }
    }

    public function afterTraverse(array $nodes)
    {
    }
}