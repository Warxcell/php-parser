<?php

namespace VM5\PhpParser\NodeVisitor;

use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\NodeVisitor;

class DocBlockVisitor implements NodeVisitor
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
     * @var Node\Name
     */
    private $namespace;

    /**
     * @var Node\Name[]
     */
    protected $aliases = [];

    /**
     * @var \VM5\PhpParser\DocBlockVisitor[]
     */
    private $docBlockVisitors;

    /**
     * DocBlockVisitor constructor.
     * @param DocBlockFactory $docBlockFactory
     * @param Serializer $docBlockSerializer
     * @param \VM5\PhpParser\DocBlockVisitor[] $docBlockVisitors
     */
    public function __construct(
        DocBlockFactory $docBlockFactory,
        Serializer $docBlockSerializer,
        $docBlockVisitors
    ) {
        $this->docBlockFactory = $docBlockFactory;
        $this->docBlockSerializer = $docBlockSerializer;
        $this->docBlockVisitors = $docBlockVisitors;
    }

    private function resetState(Node\Name $namespace = null)
    {
        $this->namespace = $namespace;
        $this->aliases = [];
    }

    public function beforeTraverse(array $nodes)
    {
        $this->resetState();
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->resetState($node->name);
        } elseif ($node instanceof Node\Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->addAlias($use, null);
            }
        } elseif ($node instanceof Node\Stmt\GroupUse) {
            foreach ($node->uses as $use) {
                $this->addAlias($use, $node->prefix);
            }
        }
    }

    protected function addAlias(Node\Stmt\UseUse $use, Node\Name $prefix = null)
    {
        $name = $prefix ? Name::concat($prefix, $use->name) : $use->name;

        $this->aliases[$use->alias] = $name;
    }

    public function leaveNode(Node $node)
    {
        $docComments = $node->getAttribute('comments');
        if ($docComments) {
            $newDocComments = [];
            foreach ($docComments as $docComment) {
                if ($docComment instanceof Doc) {
                    $namespace = $this->namespace->toString();

                    $aliases = [];
                    foreach ($this->aliases as $alias => $full) {
                        $aliases[$alias] = $full->toString();
                    }

                    $context = new Context($namespace, $aliases);
                    $location = new Location($docComment->getLine());
                    $docBlock = $this->docBlockFactory->create($docComment->getText(), $context, $location);

                    foreach ($this->docBlockVisitors as $visitor) {
                        $newDocBlock = $visitor->visit($docBlock);
                        if ($newDocBlock) {
                            $docBlock = $newDocBlock;
                        }
                    }

                    $newDocCommentText = $this->docBlockSerializer->getDocComment($docBlock);

                    $class = get_class($docComment);
                    $newDocComment = new $class($newDocCommentText);
                    $newDocComments[] = $newDocComment;

                }
            }
            $node->setAttribute('comments', $newDocComments);
        }
    }

    public function afterTraverse(array $nodes)
    {
    }
}