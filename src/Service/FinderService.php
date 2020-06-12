<?php

namespace App\Service;

use App\Interfaces\Event\EventInterface;
use Symfony\Component\Finder\Finder;

class FinderService
{
    /**
     * @var string
     */
    private $defaultEventsPath = __DIR__.'/../Event/*';

    /**
     * @var Finder
     */
    private $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @return array
     */
    public static function loadEvents(): array
    {
        $events = [];

        $finder = new Finder();
        $finderService = new static ($finder);

        $finder->files()->in($finderService->defaultEventsPath);

        // check if there are any search results
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                list($fileName, $extension) = explode('.', $file->getRelativePathname());

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
            }
        }

        return $events;
    }

    /**
     * @param string $eventName
     * @param bool   $strict
     *
     * @return array
     */
    public static function getEventsByInterface(string $eventName, bool $strict = false): array
    {
        $events = [];

        /** @var EventInterface $key */
        foreach (self::loadEvents() as $key => $event) {
            if (\count($event) < 1 && $strict) {
                if (in_array($eventName, $event)) {
                    $events = array_merge($events, $key::getEventsName());
                }
            } elseif (in_array($eventName, $event) && !$strict) {
                $events = array_merge($events, $key::getEventsName());
            }
        }

        return $events;
    }
}
