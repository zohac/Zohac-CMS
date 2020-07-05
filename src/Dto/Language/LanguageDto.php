<?php

namespace App\Dto\Language;

use Symfony\Component\Validator\Constraints as Assert;

class LanguageDto
{
    /**
     * @Assert\Length(
     *      max = 255,
     *      minMessage = "Your language name must be at least {{ limit }} characters long",
     *      maxMessage = "Your language name cannot be longer than {{ limit }} characters",
     *     allowEmptyString = true
     * )
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @Assert\Length(
     *      max = 255,
     *      minMessage = "Your language alternate name must be at least {{ limit }} characters long",
     *      maxMessage = "Your language alternate name cannot be longer than {{ limit }} characters",
     *     allowEmptyString = true
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
    public $iso639_1;

    /**
     * @Assert\Length(
     *      min = 3,
     *      max = 3,
     *      minMessage = "The iso639_2/T string must be {{ limit }} characters long",
     *      maxMessage = "The iso639_2/T string must be {{ limit }} characters long",
     *     allowEmptyString = true
     * )
     */
    public $iso639_2T;

    /**
     * @Assert\Length(
     *      min = 3,
     *      max = 3,
     *      minMessage = "The iso639_2/B string must be {{ limit }} characters long",
     *      maxMessage = "The iso639_2/B string must be {{ limit }} characters long",
     *     allowEmptyString = true
     * )
     */
    public $iso639_2B;

    public $iso639_3 = [];
}
