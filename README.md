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

$removeComments = new \VM5\PhpCommentsRemover\CommentsRemover(
    $parserFactory->create(\PhpParser\ParserFactory::PREFER_PHP5),
    new NodeTraverser,
    new Visitor($docBlockFactory, $serializer),
    new PrettyPrinter\Standard,
    new SameFileSaver()
);

$removeComments->removeFromDirectory(__DIR__);
$removeComments->removeFromFile('file.php');
```