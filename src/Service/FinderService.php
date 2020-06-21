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

    /**
     * @var ReaderFileService
     */
    private $readerFile;

    public function __construct(Finder $finder, ReaderFileService $readerFile)
    {
        $this->finder = $finder;
        $this->readerFile = $readerFile;
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

        $readerFile = new ReaderFileService();
        $finder = new Finder();
        $finderService = new static ($finder, $readerFile);

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
            $events = array_merge(
                $events,
                $this->readerFile->readAndRegexSearchInFileContent($file, '/namespace ([a-zA-Z\\\]*)\;/')
            );
        }

        return $events;
    }
}
