<?php

namespace App\Service\Language;

use App\Dto\Language\LanguageDto;
use App\Entity\Language;
use App\Event\Language\LanguageEvent;
use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Event\EventInterface;
use App\Interfaces\Event\ViewEventInterface;
use App\Interfaces\Service\ServiceInterface;
use App\Service\EntityService;
use App\Service\EventService;
use App\Service\FlashBagService;
use ReflectionException;

class LanguageService implements ServiceInterface
{
    const ENTITY_NAME = Language::class;

    /**
     * @var EventService
     */
    private $eventService;

    /**
     * @var string
     */
    private $formType;

    /**
     * @var DtoInterface
     */
    private $dto;

    /**
     * @var FlashBagService
     */
    private $flashBagService;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * LanguageService constructor.
     *
     * @param EventService    $eventService
     * @param FlashBagService $flashBagService
     * @param EntityService   $entityService
     *
     * @throws ReflectionException
     */
    public function __construct(
        EventService $eventService,
        FlashBagService $flashBagService,
        EntityService $entityService
    ) {
        $this->eventService = $eventService;
        $this->flashBagService = $flashBagService;
        $this->entityService = $entityService;

        $this->reflectionClass = $this->entityService->getNewReflectionClass(self::ENTITY_NAME);
    }

    /**
     * @param LanguageDto $languageDto
     *
     * @return Language
     *
     * @throws ReflectionException
     * @throws UuidException
     */
    public function createLanguageFromDto(LanguageDto $languageDto): Language
    {
        $language = new Language();

        /** @var Language $language */
        $language = $this->entityService->populateEntityWithDto($language, $languageDto);

        $this->eventService->dispatchEvent(LanguageEvent::POST_CREATE, [
            $this->getEntityNameToLower() => $language,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            'Language',
            'Language successfully created.',
            $this->getEntityNameToLower()
        );

        return $language;
    }

    /**
     * @return string
     */
    public function getEntityNameToLower(): string
    {
        return strtolower($this->reflectionClass->getShortName());
    }

    /**
     * @param Language $language
     *
     * @return LanguageDto
     *
     * @throws ReflectionException
     */
    public function createLanguageDtoFromLanguage(Language $language): LanguageDto
    {
        $languageDto = new LanguageDto();

        $this->entityService->populateDtoWithEntity($language, $languageDto);

        return $languageDto;
    }

    /**
     * @param LanguageDto $languageDto
     * @param Language    $language
     *
     * @return Language
     *
     * @throws UuidException
     * @throws ReflectionException
     */
    public function updateLanguageFromDto(LanguageDto $languageDto, Language $language): Language
    {
        /** @var Language $language */
        $language = $this->entityService->populateEntityWithDto($language, $languageDto);

        $this->eventService->dispatchEvent(LanguageEvent::POST_UPDATE, [
            $this->getEntityNameToLower() => $language,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            'Language',
            'Language successfully updated.',
            $this->getEntityNameToLower()
        );

        return $language;
    }

    /**
     * @param Language $language
     *
     * @return $this
     */
    public function deleteLanguage(Language $language): self
    {
        $this->entityService
            ->remove($language)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'Language',
            'Language successfully deleted.',
            $this->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(LanguageEvent::POST_DELETE);

        return $this;
    }

    /**
     * @return string
     */
    public function getFormType(): string
    {
        return $this->formType;
    }

    /**
     * @param $formType
     *
     * @return $this
     */
    public function setFormType(string $formType): self
    {
        $this->formType = $formType;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityNamePlural(): string
    {
        return strtolower($this->reflectionClass->getShortName().'s');
    }

    /**
     * @return DtoInterface
     */
    public function getDto(): DtoInterface
    {
        return $this->dto;
    }

    /**
     * @param DtoInterface $dto
     *
     * @return $this
     */
    public function setDto(DtoInterface $dto): self
    {
        $this->dto = $dto;

        return $this;
    }

    /**
     * @return EventInterface
     */
    public function getEvent(): EventInterface
    {
        $events = $this->getEventService()->getEvents();

        return $events[$this->getEntityName()];
    }

    /**
     * @return EventService
     */
    public function getEventService(): EventService
    {
        return $this->eventService;
    }

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->reflectionClass->getShortName();
    }

    /**
     * @return ViewEventInterface
     */
    public function getViewEvent(): ViewEventInterface
    {
        $viewEvents = $this->getEventService()->getViewEvents();

        return $viewEvents[$this->getEntityName()];
    }

    /**
     * @return EntityInterface
     */
    public function getEntity(): EntityInterface
    {
        return $this->language;
    }

    /**
     * @param EntityInterface $language
     *
     * @return $this
     */
    public function setEntity(EntityInterface $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getDeleteMessage(): string
    {
        return $this->flashBagService->trans(
            'Are you sure you want to delete this language (%language%) ?',
            $this->getEntityNameToLower(),
            [$this->getEntityNameToLower() => $this->language->getIso6391()]
        );
    }
}
