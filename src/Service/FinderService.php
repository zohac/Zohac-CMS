<?php

namespace App\Service;

use App\Interfaces\Event\EventInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FinderService
{
    /**
     * @var string
     */
    private $defaultEventsPath = __DIR__.'/../Event';

    /**
     * @var Finder
     */
    private $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

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
     * @param string      $eventName
     * @param string|null $path
     *
     * @return array
     */
    public static function getEventsByInterface(string $eventName, ?string $path = null): array
    {
        $events = [];

        /** @var EventInterface $key */
        foreach (self::loadEvents($path) as $key => $event) {
            if (in_array($eventName, $event) && method_exists($key, 'getEventsName')) {
                $events = array_merge($events, $key::getEventsName());
            }
        }

        return $events;
    }

    /**
     * @param string|null $path
     *
     * @return array
     */
    public static function loadEvents(?string $path = null): array
    {
        $events = [];

        $finder = new Finder();
        $finderService = new static ($finder);

        $path = $path ?? $finderService->defaultEventsPath;

        $finder->files()->in($path);

        // check if there are any search results
        if ($finder->hasResults()) {
            $events = $finderService->getEventsInFinder($finder);
        }

        return $events;
    }

    /**
     * @param Finder $finder
     *
     * @return array
     */
    public function getEventsInFinder(Finder $finder): array
    {
        $events = [];

        foreach ($finder as $file) {
            $events = $this->readFileContent($file);
        }

        return $events;
    }

    /**
     * @param SplFileInfo $file
     *
     * @return array
     */
    public function readFileContent(SplFileInfo $file): array
    {
        $events = [];
        $fileName = $this->getFilename($file->getRelativePathname());

        $handle = @fopen($file->getRealPath(), 'r');
        if ($handle) {
            $continue = true;
            while (false !== ($buffer = fgets($handle, 4096)) && $continue) {
                if (preg_match('/namespace ([a-zA-Z\\\]*)\;/', $buffer, $matches)) {
                    $className = $matches[1].'\\'.$fileName;

                    $events[$className] = class_implements($className);

                    $continue = false;
                }
            }

            fclose($handle);
        }

        return $events;
    }
}
