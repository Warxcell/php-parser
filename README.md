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

$docBlockVisitors = [
    new \VM5\PhpParser\DocBlockVisitor\CommentsRemoverDocBlockVisitor(),
];

$visitor = new \VM5\PhpParser\NodeVisitor\DocBlockVisitor($docBlockFactory, $serializer, $docBlockVisitors);
$nodeTraverser = new NodeTraverser();
$nodeTraverser->addVisitor($visitor);

$removeComments = new \VM5\PhpCommentsRemover\CommentsRemover(
    $parserFactory->create(\PhpParser\ParserFactory::PREFER_PHP5),
    $traverser,
    new PrettyPrinter\Standard,
    new SameFileSaver()
);

$removeComments->parseDirectory(__DIR__);
$removeComments->parseFile('file.php');
```