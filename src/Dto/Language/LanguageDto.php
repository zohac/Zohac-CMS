<?php

namespace App\Dto\Language;

use App\Entity\Language;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LanguageDto implements DtoInterface
{
    /**
     * @Assert\Uuid()
     */
    public $uuid;

    /**
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "Your language name must be at least {{ limit }} characters long",
     *      maxMessage = "Your language name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = true
     * )
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @Assert\Length(
     *      max = 255,
     *      minMessage = "Your language alternate name must be at least {{ limit }} characters long",
     *      maxMessage = "Your language alternate name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = true
     * )
     */
    public $alternateName;

    /**
     * @Assert\Type("string")
     */
    public $description;

    /**
     * @Assert\Length(
     *      min = 2,
     *      max = 2,
     *      minMessage = "The iso639_1 string must be {{ limit }} characters long",
     *      maxMessage = "The iso639_1 string must be {{ limit }} characters long",
     *      allowEmptyString = false
     * )
     * @Assert\NotBlank()
     */
    public $iso6391;

    /**
     * @Assert\Length(
     *      min = 3,
     *      max = 3,
     *      minMessage = "The iso639_2/T string must be {{ limit }} characters long",
     *      maxMessage = "The iso639_2/T string must be {{ limit }} characters long",
     *     allowEmptyString = true
     * )
     */
    public $iso6392T;

    /**
     * @Assert\Length(
     *      min = 3,
     *      max = 3,
     *      minMessage = "The iso639_2/B string must be {{ limit }} characters long",
     *      maxMessage = "The iso639_2/B string must be {{ limit }} characters long",
     *      allowEmptyString = true
     * )
     */
    public $iso6392B;

    public $iso6393 = [];

    /**
     * @param EntityInterface $entity
     *
     * @return bool
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof Language;
    }
}
