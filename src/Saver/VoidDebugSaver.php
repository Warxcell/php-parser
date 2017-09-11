<?php
/**
 * Created by PhpStorm.
 * User: bozhidar.hristov
 * Date: 9/11/17
 * Time: 12:50 PM
 */

namespace VM5\PhpCommentsRemover\Saver;


class VoidDebugSaver implements \VM5\PhpCommentsRemover\Saver
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