<?php
/**
 * Created by PhpStorm.
 * User: bozhidar.hristov
 * Date: 9/11/17
 * Time: 12:50 PM
 */

namespace VM5\PhpCommentsRemover\Saver;


class VoidEchoSaver implements \VM5\PhpCommentsRemover\Saver
{
    public function save(\SplFileInfo $fileInfo, $code)
    {
        $debug = $fileInfo->getPathname().': '.mb_strlen($code).PHP_EOL.
            $code.PHP_EOL.
            'end of '.$fileInfo->getPathname().PHP_EOL;
        echo $debug;
    }
}