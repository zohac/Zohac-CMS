<?php

namespace App\Service;

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
     * @param $handle "stream resource"
     * @param string $pattern
     */
    public function regexSearchInFileContent($handle, string $pattern, $fileName): array
    {
        $events = [];
        $continue = true;

        while (false !== ($buffer = fgets($handle, 4096)) && $continue) {
            if (preg_match($pattern, $buffer, $matches)) {
                $className = $matches[1].'\\'.$fileName;

                $events[$className] = class_implements($className);

                $continue = false;
            }
        }

        return $events;
    }

    /**
     * @param SplFileInfo $file
     *
     * @return array
     */
    public function readAndRegexSearchInFileContent(SplFileInfo $file, string $pattern): array
    {
//        $fileObject = $file->openFile();
//        dump($fileObject);
        $events = [];
        $fileName = $this->getFilename($file->getRelativePathname());

        $handle = @fopen($file->getRealPath(), 'r');
        if ($handle) {
            $events = $this->regexSearchInFileContent($handle, '/namespace ([a-zA-Z\\\]*)\;/', $fileName);

            fclose($handle);
        }

        return $events;
    }
}