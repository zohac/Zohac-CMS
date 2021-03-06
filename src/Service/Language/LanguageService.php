<?php

namespace App\Service\Language;

use App\Dto\Language\LanguageDto;
use App\Entity\Language;
use App\Event\Language\LanguageEvent;
use App\Exception\HydratorException;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\ServiceInterface;
use App\Repository\LanguageRepository;
use App\Service\EntityService;
use App\Service\EventService;
use App\Service\FlashBagService;
use ReflectionException;

class LanguageService implements ServiceInterface
{
    /**
     * @var EventService
     */
    private $eventService;

    /**
     * @var FlashBagService
     */
    private $flashBagService;

    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * LanguageService constructor.
     *
     * @param EventService       $eventService
     * @param FlashBagService    $flashBagService
     * @param EntityService      $entityService
     * @param LanguageRepository $languageRepository
     */
    public function __construct(
        EventService $eventService,
        FlashBagService $flashBagService,
        EntityService $entityService,
        LanguageRepository $languageRepository
    ) {
        $this->eventService = $eventService;
        $this->flashBagService = $flashBagService;
        $this->entityService = $entityService;
        $this->languageRepository = $languageRepository;
    }

    /**
     * @param LanguageDto $languageDto
     *
     * @return Language
     *
     * @throws HydratorException
     */
    public function createLanguageFromDto(LanguageDto $languageDto): Language
    {
        /** @var Language $language */
        $language = $this->entityService->hydrateEntityWithDto(new Language(), $languageDto);

        $this->eventService->dispatchEvent(LanguageEvent::POST_CREATE, [
            'language' => $language,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            'Language',
            'Language successfully created.',
            'language'
        );

        return $language;
    }

    /**
     * @param LanguageDto $languageDto
     * @param Language    $language
     *
     * @return Language
     *
     * @throws HydratorException
     */
    public function updateLanguageFromDto(LanguageDto $languageDto, Language $language): Language
    {
        /** @var Language $language */
        $language = $this->entityService->hydrateEntityWithDto($language, $languageDto);

        $this->eventService->dispatchEvent(LanguageEvent::POST_UPDATE, [
            'language' => $language,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            'Language',
            'Language successfully updated.',
            'language'
        );

        return $language;
    }

    /**
     * @param Language $language
     *
     * @return $this
     *
     * @throws ReflectionException
     */
    public function deleteLanguage(Language $language): self
    {
        $this->entityService
            ->setEntity($language)
            ->remove($language)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'Language',
            'Language successfully deleted.',
            $this->entityService->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(LanguageEvent::POST_DELETE);

        return $this;
    }

    /**
     * @param Language $language
     *
     * @return $this
     *
     * @throws ReflectionException
     */
    public function deleteSoftLanguage(Language $language)
    {
        $language->setArchived(true);

        $this->entityService
            ->setEntity($language)
            ->persist($language)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'Language',
            'Language successfully deleted.',
            $this->entityService->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(LanguageEvent::POST_DELETE);

        return $this;
    }

    /**
     * @return array
     */
    public function getUuidLanguages(): array
    {
        return $this->languageRepository->findAllUuid(['archived' => false]);
    }

    /**
     * @return array
     */
    public function getLanguagesForForm(): array
    {
        $languages = $this->languageRepository->findLanguagesForForm(['archived' => false]);

        $languagesForForm = [];
        foreach ($languages as $language) {
            $languagesForForm[$language['iso6391']] = $language['uuid'];
        }

        return $languagesForForm;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return string
     */
    public function getDeleteMessage(EntityInterface $entity): string
    {
        /* @var Language $entity */
        return $this->flashBagService->trans(
            'Are you sure you want to delete this language (%language%) ?',
            'language',
            ['language' => $entity->getName()]
        );
    }
}
