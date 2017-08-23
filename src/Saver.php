<?php

namespace VM5\PhpCommentsRemover;

/**
 * Interface Saver
 * @package VM5\PhpCommentsRemover
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