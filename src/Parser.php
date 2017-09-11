<?php

namespace VM5\PhpParser;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser as PHPParser;
use PhpParser\PrettyPrinterAbstract;

class Parser
{
    /**
     * @var PHPParser
     */
    private $phpParser;

    /**
     * @var NodeTraverser
     */
    private $nodeTraverser;

    /**
     * @var PrettyPrinterAbstract
     */
    private $phpDumper;

    /**
     * @var Saver
     */
    private $saver;

    /**
     * CommentsRemover constructor.
     * @param PHPParser $phpParser
     * @param NodeTraverser $nodeTraverser
     * @param PrettyPrinterAbstract $phpDumper
     * @param Saver $saver
     */
    public function __construct(
        PHPParser $phpParser,
        NodeTraverser $nodeTraverser,
        PrettyPrinterAbstract $phpDumper,
        Saver $saver
    ) {
        $this->phpParser = $phpParser;
        $this->nodeTraverser = $nodeTraverser;
        $this->phpDumper = $phpDumper;
        $this->saver = $saver;
    }


    public function parseDirectory($directory)
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        $phpFiles = new \RegexIterator($files, '/\.php$/');

        foreach ($phpFiles as $file) {
            /** @var $file \SplFileInfo */
            $this->removeFromFile($file);
        }
    }

    public function parseFile(\SplFileInfo $fileInfo)
    {
        try {
            $stmts = $this->phpParser->parse(file_get_contents($fileInfo->getPathname()));
            $stmts = $this->nodeTraverser->traverse($stmts);
            $code = $this->phpDumper->prettyPrintFile($stmts);

            $this->saver->save($fileInfo, $code);
        } catch (\Exception $ex) {
            throw new Exception('Error while processing '.$fileInfo->getPathname(), 0, $ex);
        }
    }
}