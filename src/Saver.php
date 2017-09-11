<?php

namespace VM5\PhpParser;

/**
 * Interface Saver
 * @package VM5\PhpParser
 */
interface Saver
{
    /**
     * @param \SplFileInfo $fileInfo
     * @param $code
     * @return null
     */
    public function save(\SplFileInfo $fileInfo, $code);
}