<?php

namespace App\Service\Translatable;

use App\Dto\Translation\TranslationDto;
use App\Entity\Translatable;
use App\Exception\UuidException;
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
     * TranslatableService constructor.
     *
     * @param UuidService  $uuidService
     * @param Translatable $translatable
     */
    public function __construct(Translatable $translatable, UuidService $uuidService)
    {
        $this->uuidService = $uuidService;
        $this->translatable = $translatable;
    }

    /**
     * @param Translatable|null $translatable
     *
     * @return Translatable
     *
     * @throws UuidException
     */
    public function hydrateTranslatable(?Translatable $translatable): Translatable
    {
        $translatable = ($translatable instanceof Translatable) ?
            $translatable :
            $this->getNewTranslatable()->setUuid($this->getUuid());

        foreach ($translatable->getTranslations() as $translation) {
            $translatable->removeTranslation($translation);
        }

        /* @var TranslationDto $translationDto */
        foreach ($translatable as $translationDto) {
            $translatable->addTranslation($translation);
        }

        return $translatable;
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

    /**
     * @return Translatable
     */
    public function getNewTranslatable(): Translatable
    {
        return clone $this->translatable;
    }
}
