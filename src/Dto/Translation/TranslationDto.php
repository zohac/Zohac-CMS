<?php

namespace App\Dto\Translation;

use App\Entity\Translation;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TranslationDto implements DtoInterface
{
    /**
     * @Assert\Uuid()
     */
    public $uuid;

    /**
     * @Ass
     */
    public $message;

    /**
     * @Assert\Choice(callback={"App\Service\Language\LanguageService", "getUuidLanguages"})
     */
    public $language;

    /**
     * {@inheritdoc}
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof Translation;
    }
}
