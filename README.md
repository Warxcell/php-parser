Sample usage:



```php
<?php

use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter;
use VM5\PhpCommentsRemover\Saver\SameFileSaver;
use VM5\PhpCommentsRemover\Visitor;

require 'vendor/autoload.php';

$parserFactory = new \PhpParser\ParserFactory();

$docBlockFactory = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
$serializer = new \phpDocumentor\Reflection\DocBlock\Serializer();

$traverser = new NodeTraverser;
$traverser->addVisitor(new \VM5\PhpParser\CommentsRemoverVisitor($docBlockFactory, $serializer));

$removeComments = new \VM5\PhpCommentsRemover\CommentsRemover(
    $parserFactory->create(\PhpParser\ParserFactory::PREFER_PHP5),
    $traverser,
    new PrettyPrinter\Standard,
    new SameFileSaver()
);

$removeComments->parseDirectory(__DIR__);
$removeComments->parseFile('file.php');
```