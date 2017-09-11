<?php

namespace VM5\PhpParser\Saver;


class VoidDebugSaver implements \VM5\PhpParser\Saver
{
    /**
     * @var array
     */
    private $files = [];

    public function save(\SplFileInfo $fileInfo, $code)
    {
        $this->files[$fileInfo->getPathname()] = $code;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }
}