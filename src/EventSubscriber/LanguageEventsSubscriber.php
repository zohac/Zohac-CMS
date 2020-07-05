<?php

namespace App\EventSubscriber;

use App\Event\Language\LanguageEvent;
use App\Service\Language\LanguageService;
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
            LanguageEvent::PRE_UPDATE => ['onLanguagePreUpdate', 0],
            LanguageEvent::UPDATE => ['onLanguageUpdate', 0],
            LanguageEvent::DELETE => ['onLanguageDelete', 0],
        ];
    }

    /**
     * @param LanguageEvent $event
     */
    public function onLanguageCreate(LanguageEvent $event)
    {
        $this->languageService->createLanguageFromDto($event->getLanguageDto());
    }

    /**
     * @param LanguageEvent $event
     */
    public function onLanguagePreUpdate(LanguageEvent $event)
    {
    }

    /**
     * @param LanguageEvent $event
     */
    public function onLanguageUpdate(LanguageEvent $event)
    {
        $this->languageService->updateLanguageFromDto($event->getLanguageDto(), $event->getLanguage());
    }

    /**
     * @param LanguageEvent $event
     */
    public function onLanguageDelete(LanguageEvent $event)
    {
        $this->languageService->deleteLanguage($event->getLanguage());
    }
}