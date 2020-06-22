<?php

namespace App\Service;

use SplFileObject;
use Symfony\Component\Finder\SplFileInfo;

class ReaderFileService
{
    /**
     * @param string $relativePath
     *
     * @return string
     */
    public function getFilename(string $relativePath): string
    {
        preg_match('/([a-zA-Z]*).php/', $relativePath, $fileName);

        return $fileName[1];
    }

    /**
     * @param SplFileObject $fileObject
     * @param string        $pattern
     *
     * @return array
     */
    public function regexSearchInFileContent(SplFileObject $fileObject, string $pattern): array
    {
        $events = [];
        $continue = true;

        while (false !== ($buffer = $fileObject->fread(4096)) && $continue) {
            if (preg_match($pattern, $buffer, $matches)) {
                $className = $matches[1].'\\'.$this->getFilename($fileObject->getFilename());

                $events[$className] = class_implements($className);

                $continue = false;
            }
        }

        return $events;
    }

    /**
     * @param SplFileInfo $file
     * @param string      $pattern
     *
     * @return array
     */
    public function readAndRegexSearchInFileContent(SplFileInfo $file, string $pattern): array
    {
        $events = [];

        if ($fileObject = $file->openFile()) {
            $events = $this->regexSearchInFileContent($fileObject, '/namespace ([a-zA-Z\\\]*)\;/');

            // Close the file
            $fileObject = null;
        }

        return $events;
    }
}
