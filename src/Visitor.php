<?php

namespace VM5\PhpCommentsRemover;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Types\Context;
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
     * @var Node\Name
     */
    private $namespace;

    /**
     * @var Node\Name[]
     */
    protected $aliases = [];

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
                    $da = $this->docBlockFactory->create($docComment->getText(), $context, $location);

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