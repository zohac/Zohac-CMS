<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\TranslatorService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var TranslatorService
     */
    private $translatorService;

    /**
     * LocaleSubscriber constructor.
     *
     * @param TranslatorService $translatorService
     */
    public function __construct(TranslatorService $translatorService)
    {
        $this->translatorService = $translatorService;
    }

    /**
     * On kernel Request, we get the user Locale OR set the default Locale.
     *
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        /** @var User $user */
        if ($user = $request->getUser()) {
            $locale = $user->getLocale();

            $this->translatorService->setLocale($locale);
            $request->setLocale($locale);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
