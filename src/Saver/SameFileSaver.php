<?php

namespace VM5\PhpCommentsRemover\Saver;

use VM5\PhpCommentsRemover\Saver;

class SameFileSaver implements Saver
{
    public function save(\SplFileInfo $fileInfo, $code)
    {
        file_put_contents($fileInfo->getPathname(), $code);
    }
}