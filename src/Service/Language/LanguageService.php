<?php

namespace App\Service\Language;

use App\Dto\Language\LanguageDto;
use App\Entity\Language;
use App\Event\Language\LanguageEvent;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\Service\ServiceInterface;
use App\Service\EventService;
use App\Service\FlashBagService;
use Doctrine\ORM\EntityManagerInterface;

class LanguageService implements ServiceInterface
{
    /**
     * @var EventService
     */
    private $eventService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $formType;

    /**
     * @var string
     */
    private $entityName;

    /**
     * @var DtoInterface
     */
    private $dto;

    /**
     * @var FlashBagService
     */
    private $flashBagService;

    private $language;

    public function __construct(
        EventService $eventService,
        EntityManagerInterface $entityManager,
        FlashBagService $flashBagService
    ) {
        $this->eventService = $eventService;
        $this->entityManager = $entityManager;
        $this->flashBagService = $flashBagService;
    }

    /**
     * @param LanguageDto $languageDto
     *
     * @return Language
     */
    public function createLanguageFromDto(LanguageDto $languageDto): Language
    {
        $language = new Language();

        $language = $this->populateLanguageWithDto($language, $languageDto);

        $this->eventService->dispatchEvent(LanguageEvent::POST_CREATE, ['language' => $language]);

        $this->flashBagService->addAndTransFlashMessage(
            'Language',
            'Language successfully created.',
            'language'
        );

        return $language;
    }

    /**
     * @param Language    $language
     * @param LanguageDto $languageDto
     *
     * @return Language
     */
    public function populateLanguageWithDto(Language $language, LanguageDto $languageDto): Language
    {
        $language->setName($languageDto->name)
            ->setAlternateName($languageDto->alternateName)
            ->setDescription($languageDto->description)
            ->setIso6391($languageDto->iso6391)
            ->setIso6392B($languageDto->iso6392B)
            ->setIso6392T($languageDto->iso6392T)
            ->setIso6393($languageDto->iso6393);

        $this->entityManager->persist($language);
        $this->entityManager->flush();

        return $language;
    }

    /**
     * @param Language $language
     *
     * @return LanguageDto
     */
    public function createLanguageDtoFromLanguage(Language $language): LanguageDto
    {
        $languageDto = new LanguageDto();

        $languageDto->name = $language->getName();
        $languageDto->alternateName = $language->getAlternateName();
        $languageDto->description = $language->getDescription();
        $languageDto->iso6391 = $language->getIso6391();
        $languageDto->iso6392B = $language->getIso6392B();
        $languageDto->iso6392T = $language->getIso6392T();
        $languageDto->iso6393 = $language->getIso6393();

        return $languageDto;
    }

    /**
     * @param LanguageDto $languageDto
     * @param Language    $language
     *
     * @return Language
     */
    public function updateLanguageFromDto(LanguageDto $languageDto, Language $language): Language
    {
        $language = $this->populateLanguageWithDto($language, $languageDto);

        $this->eventService->dispatchEvent(LanguageEvent::POST_UPDATE, ['language' => $language]);

        return $language;
    }

    /**
     * @param Language $language
     *
     * @return $this
     */
    public function deleteLanguage(Language $language): self
    {
        $this->entityManager->remove($language);
        $this->entityManager->flush();

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
    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * @param $entityName
     *
     * @return $this
     */
    public function setEntityName($entityName): self
    {
        $this->entityName = $entityName;

        return $this;
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
     * @return EventService
     */
    public function getEventService(): EventService
    {
        return $this->eventService;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setEntity($language): self
    {
        $this->language = $language;

        return $this;
    }
}
