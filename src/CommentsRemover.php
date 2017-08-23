<?php

namespace VM5\PhpCommentsRemover;

class CommentsRemover
{
    /**
     * @var \PhpParser\Parser
     */
    private $phpParser;

    /**
     * @var NodeTraverser
     */
    private $nodeTraverser;

    /**
     * @var \PhpParser\PrettyPrinterAbstract
     */
    private $phpDumper;

    /**
     * @var Saver
     */
    private $saver;

    /**
     * RemoveComments constructor.
     * @param \PhpParser\Parser $phpParser
     */
    public function __construct(
        \PhpParser\Parser $phpParser,
        NodeTraverser $nodeTraverser,
        Comments $commentsRemoverTraverser,
        \PhpParser\PrettyPrinterAbstract $phpDumper,
        Saver $saver
    ) {
        $this->phpParser = $phpParser;
        $this->nodeTraverser = $nodeTraverser;
        $this->nodeTraverser->addVisitor($commentsRemoverTraverser);
        $this->phpDumper = $phpDumper;
    }


    public function removeFromDirectory($directory)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        $phpFiles = new RegexIterator($files, '/\.php$/');

        foreach ($phpFiles as $file) {
            /** @var $file SplFileInfo */
            $this->removeFromFile($file);
        }
    }

    public function removeFromFile(SplFileInfo $fileInfo)
    {
        $stmts = $this->phpParser->parse(file_get_contents($fileInfo->getPathname()));
        $stmts = $this->nodeTraverser->traverse($stmts);
        $code = $this->phpDumper->prettyPrint($stmts);

        $this->saver->save($fileInfo, $code);
    }
}