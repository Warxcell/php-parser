<?php

namespace VM5\PhpCommentsRemover;

class Traverser implements \PhpParser\NodeVisitor
{
    /**
     * @var \phpDocumentor\Reflection\DocBlockFactory
     */
    private $docBlockFactory;

    /**
     * @var \phpDocumentor\Reflection\DocBlock\Serializer
     */
    private $docBlockSerializer;

    /**
     * Comments constructor.
     * @param \phpDocumentor\Reflection\DocBlockFactory $docBlockFactory
     * @param \phpDocumentor\Reflection\DocBlock\Serializer $docBlockSerializer
     */
    public function __construct(
        \phpDocumentor\Reflection\DocBlockFactory $docBlockFactory,
        \phpDocumentor\Reflection\DocBlock\Serializer $docBlockSerializer
    ) {
        $this->docBlockFactory = $docBlockFactory;
        $this->docBlockSerializer = $docBlockSerializer;
    }


    public function beforeTraverse(array $nodes)
    {
    }

    public function enterNode(\PhpParser\Node $node)
    {

    }

    public function leaveNode(\PhpParser\Node $node)
    {
        $docComments = $node->getAttribute('comments');
        if ($docComments) {
            $newDocComments = [];
            foreach ($docComments as $docComment) {
                if ($docComment instanceof \PhpParser\Comment\Doc) {
                    $da = $this->docBlockFactory->create($docComment->getText());

                    $newTags = [];

                    $excluded = ['author', 'copyright'];
                    foreach ($da->getTags() as $tag) {
                        if (!in_array($tag->getName(), $excluded)) {
                            $newTags[] = $tag;
                        }
                    }

                    if (count($newTags) != 0) {
                        $cloned = new \phpDocumentor\Reflection\DocBlock(
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