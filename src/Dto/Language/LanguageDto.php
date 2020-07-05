<?php

namespace App\Dto\Language;

use Symfony\Component\Validator\Constraints as Assert;

class LanguageDto
{
    /**
     * @Assert\Length(
     *      min = 0,
     *      max = 255,
     *      minMessage = "Your language name must be at least {{ limit }} characters long",
     *      maxMessage = "Your language name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = yes
     * )
     */
    public $name;

    /**
     * @Assert\Length(
     *      min = 0,
     *      max = 255,
     *      minMessage = "Your language alternate name must be at least {{ limit }} characters long",
     *      maxMessage = "Your language alternate name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = yes
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
     */
    public $iso639_1;

    /**
     * @Assert\Length(
     *      min = 3,
     *      max = 3,
     *      minMessage = "The iso639_2/T string must be {{ limit }} characters long",
     *      maxMessage = "The iso639_2/T string must be {{ limit }} characters long",
     *      allowEmptyString = yes
     * )
     */
    public $iso639_2T;

    /**
     * @Assert\Length(
     *      min = 3,
     *      max = 3,
     *      minMessage = "The iso639_2/B string must be {{ limit }} characters long",
     *      maxMessage = "The iso639_2/B string must be {{ limit }} characters long",
     *      allowEmptyString = yes
     * )
     */
    public $iso639_2B;

    public $iso639_3 = [];
}
