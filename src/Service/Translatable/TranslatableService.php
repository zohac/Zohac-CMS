<?php

namespace App\Service\Translatable;

use App\Entity\Translatable;
use App\Exception\UuidException;
use App\Service\Translation\TranslationService;
use App\Service\UuidService;

class TranslatableService
{
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * @var Translatable
     */
    private $translatable;
    /**
     * @var TranslationService
     */
    private $translationService;

    /**
     * TranslatableService constructor.
     *
     * @param UuidService        $uuidService
     * @param TranslationService $translationService
     */
    public function __construct(
        UuidService $uuidService,
        TranslationService $translationService
    ) {
        $this->uuidService = $uuidService;
        $this->translationService = $translationService;
        $this->translatable = new Translatable();
    }

    /**
     * @param Translatable|null $translatable
     * @param array             $translations
     *
     * @return Translatable
     *
     * @throws UuidException
     */
    public function hydrateTranslatable(?Translatable $translatable, array $translations): Translatable
    {
        $translatable = ($translatable instanceof Translatable) ?
            $translatable :
            $this->getNewTranslatable()->setUuid($this->getUuid());

        foreach ($translatable->getTranslations() as $translation) {
            $translatable->removeTranslation($translation);
        }

        foreach ($translations as $translation) {
            $translation = $this->translationService->createTranslationFromArray($translation);

            $translatable->addTranslation($translation);
        }

        return $translatable;
    }

    /**
     * @return Translatable
     */
    public function getNewTranslatable(): Translatable
    {
        return clone $this->translatable;
    }

    /**
     * @return string
     *
     * @throws UuidException
     */
    public function getUuid(): string
    {
        return $this->uuidService->create();
    }
}
