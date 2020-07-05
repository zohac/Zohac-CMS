<?php

namespace App\Service\Language;

use App\Dto\Language\LanguageDto;
use App\Entity\Language;
use App\Event\Language\LanguageEvent;
use App\Service\EventService;
use Doctrine\ORM\EntityManagerInterface;

class LanguageService
{
    /**
     * @var EventService
     */
    private $eventService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EventService $eventService, EntityManagerInterface $entityManager)
    {
        $this->eventService = $eventService;
        $this->entityManager = $entityManager;
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
        $languageDto->iso639_1 = $language->getIso6391();
        $languageDto->iso639_2B = $language->getIso6392B();
        $languageDto->iso639_2T = $language->getIso6392T();
        $languageDto->iso639_3 = $language->getIso6393();

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
            ->setIso6391($languageDto->iso639_1)
            ->setIso6392B($languageDto->iso639_2B)
            ->setIso6392T($languageDto->iso639_2T)
            ->setIso6393($languageDto->iso639_3);

        $this->entityManager->persist($language);
        $this->entityManager->flush();

        return $language;
    }
}
