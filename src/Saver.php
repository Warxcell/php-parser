<?php

namespace VM5\PhpCommentsRemover;

interface Saver
{

    public function save(\SplFileInfo $fileInfo, $code);
}