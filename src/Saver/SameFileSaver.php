<?php

namespace VM5\PhpParser\Saver;

use VM5\PhpParser\Saver;

class SameFileSaver implements Saver
{
    public function save(\SplFileInfo $fileInfo, $code)
    {
        file_put_contents($fileInfo->getPathname(), $code);
    }
}