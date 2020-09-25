<?php

namespace App\Event\Parameter;

use App\Dto\Parameter\ParameterDto;
use App\Entity\Parameter;
use App\Interfaces\Event\EventInterface;
use App\Traits\Event\EventTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ParameterEvent extends Event implements EventInterface
{
    use EventTrait;

    public const PRE_CREATE = 'parameter.pre.create';
    public const CREATE = 'parameter.create';
    public const POST_CREATE = 'parameter.post.create';
    public const PRE_UPDATE = 'parameter.pre.update';
    public const UPDATE = 'parameter.update';
    public const POST_UPDATE = 'parameter.post.update';
    public const PRE_DELETE = 'parameter.pre.delete';
    public const DELETE = 'parameter.delete';
    public const SOFT_DELETE = 'parameter.soft.delete';
    public const POST_DELETE = 'parameter.post.delete';

    const ENTITY_NAME = Parameter::class;

    /**
     * @var ParameterDto
     */
    private $parameterDto;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var Parameter
     */
    private $parameter;

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
     * @return ParameterDto
     */
    public function getParameterDto(): ParameterDto
    {
        return $this->parameterDto;
    }

    /**
     * @param ParameterDto $parameterDto
     *
     * @return $this
     */
    public function setParameterDto(ParameterDto $parameterDto): self
    {
        $this->parameterDto = $parameterDto;

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
     * @return Parameter
     */
    public function getParameter(): Parameter
    {
        return $this->parameter;
    }

    /**
     * @param Parameter $parameter
     *
     * @return $this
     */
    public function setParameter(Parameter $parameter): self
    {
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * @return string
     */
    public function getRelatedEntity(): string
    {
        return self::ENTITY_NAME;
    }
}
