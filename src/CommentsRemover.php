<?php

namespace VM5\PhpCommentsRemover;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinterAbstract;

class CommentsRemover
{
    /**
     * @var Parser
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
     * @param Parser $phpParser
     * @param NodeTraverser $nodeTraverser
     * @param Visitor $visitor
     * @param PrettyPrinterAbstract $phpDumper
     * @param Saver $saver
     */
    public function __construct(
        Parser $phpParser,
        NodeTraverser $nodeTraverser,
        Visitor $visitor,
        PrettyPrinterAbstract $phpDumper,
        Saver $saver
    ) {
        $this->phpParser = $phpParser;
        $this->nodeTraverser = $nodeTraverser;
        $this->nodeTraverser->addVisitor($visitor);
        $this->phpDumper = $phpDumper;
        $this->saver = $saver;
    }


    public function removeFromDirectory($directory)
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        $phpFiles = new \RegexIterator($files, '/\.php$/');

        foreach ($phpFiles as $file) {
            /** @var $file \SplFileInfo */
            $this->removeFromFile($file);
        }
    }

    public function removeFromFile(\SplFileInfo $fileInfo)
    {
        $stmts = $this->phpParser->parse(file_get_contents($fileInfo->getPathname()));
        $stmts = $this->nodeTraverser->traverse($stmts);
        $code = "<?php \n\n".$this->phpDumper->prettyPrint($stmts);

        $this->saver->save($fileInfo, $code);
    }
}