<?php

namespace App\EventSubscriber;

use App\Event\Language\LanguageEvent;
use App\Exception\HydratorException;
use App\Service\Language\LanguageService;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LanguageEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var LanguageService
     */
    private $languageService;

    /**
     * UserPostCreateSubscriber constructor.
     *
     * @param LanguageService $languageService
     */
    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    public static function getSubscribedEvents()
    {
        return [
            LanguageEvent::CREATE => ['onLanguageCreate', 0],
            LanguageEvent::UPDATE => ['onLanguageUpdate', 0],
            LanguageEvent::DELETE => ['onLanguageDelete', 0],
            LanguageEvent::SOFT_DELETE => ['onLanguageSoftDelete', 0],
        ];
    }

    /**
     * @param LanguageEvent $event
     *
     * @throws HydratorException
     */
    public function onLanguageCreate(LanguageEvent $event)
    {
        $this->languageService->createLanguageFromDto($event->getLanguageDto());
    }

    /**
     * @param LanguageEvent $event
     *
     * @throws HydratorException
     */
    public function onLanguageUpdate(LanguageEvent $event)
    {
        $this->languageService->updateLanguageFromDto($event->getLanguageDto(), $event->getLanguage());
    }

    /**
     * @param LanguageEvent $event
     *
     * @throws ReflectionException
     */
    public function onLanguageDelete(LanguageEvent $event)
    {
        $this->languageService->deleteLanguage($event->getLanguage());
    }

    /**
     * @param LanguageEvent $event
     *
     * @throws ReflectionException
     */
    public function onLanguageSoftDelete(LanguageEvent $event)
    {
        $this->languageService->deleteSoftLanguage($event->getLanguage());
    }
}
