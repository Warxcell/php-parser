<?php

namespace VM5\PhpParser\Saver;


class VoidEchoSaver implements \VM5\PhpParser\Saver
{
    public function save(\SplFileInfo $fileInfo, $code)
    {
        $debug = $fileInfo->getPathname().': '.mb_strlen($code).PHP_EOL.
            $code.PHP_EOL.
            'end of '.$fileInfo->getPathname().PHP_EOL;
        echo $debug;
    }
}