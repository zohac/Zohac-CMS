<?php

namespace App\Event\Language;

use App\Dto\Language\LanguageDto;
use App\Entity\Language;
use App\Interfaces\Event\EventInterface;
use App\Traits\Event\EventTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class LanguageEvent extends Event implements EventInterface
{
    use EventTrait;

    public const PRE_CREATE = 'language.pre.create';
    public const CREATE = 'language.create';
    public const POST_CREATE = 'language.post.create';
    public const PRE_UPDATE = 'language.pre.update';
    public const UPDATE = 'language.update';
    public const POST_UPDATE = 'language.post.update';
    public const PRE_DELETE = 'language.pre.delete';
    public const DELETE = 'language.delete';
    public const POST_DELETE = 'language.post.delete';

    /**
     * @var LanguageDto
     */
    private $languageDto;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var string
     */
    private $relatedEntity = 'Language';

    /**
     * @return array|string[]
     */
    public static function getEventsName(): array
    {
        return [
            self::PRE_CREATE,
            self::CREATE,
            self::POST_CREATE,
            self::PRE_UPDATE,
            self::UPDATE,
            self::POST_UPDATE,
            self::PRE_DELETE,
            self::DELETE,
            self::POST_DELETE,
        ];
    }

    /**
     * @return LanguageDto
     */
    public function getLanguageDto(): LanguageDto
    {
        return $this->languageDto;
    }

    /**
     * @param LanguageDto $languageDto
     *
     * @return $this
     */
    public function setLanguageDto(LanguageDto $languageDto): self
    {
        $this->languageDto = $languageDto;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param FormInterface $form
     *
     * @return $this
     */
    public function setForm(FormInterface $form): self
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     *
     * @return $this
     */
    public function setLanguage(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getRelatedEntity(): string
    {
        return $this->relatedEntity;
    }
}
