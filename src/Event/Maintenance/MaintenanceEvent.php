<?php

namespace App\Event\Maintenance;

use App\Dto\Maintenance\MaintenanceDto;
use App\Entity\Maintenance;
use App\Interfaces\Event\EventInterface;
use App\Traits\Event\EventTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class MaintenanceEvent extends Event implements EventInterface
{
    use EventTrait;

    public const PRE_CREATE = 'maintenance.pre.create';
    public const CREATE = 'maintenance.create';
    public const POST_CREATE = 'maintenance.post.create';
    public const PRE_UPDATE = 'maintenance.pre.update';
    public const UPDATE = 'maintenance.update';
    public const POST_UPDATE = 'maintenance.post.update';
    public const PRE_DELETE = 'maintenance.pre.delete';
    public const DELETE = 'maintenance.delete';
    public const SOFT_DELETE = 'maintenance.soft.delete';
    public const POST_DELETE = 'maintenance.post.delete';

    const ENTITY_NAME = Maintenance::class;

    /**
     * @var MaintenanceDto
     */
    private $maintenanceDto;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var Maintenance
     */
    private $maintenance;

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
     * @return MaintenanceDto
     */
    public function getMaintenanceDto(): MaintenanceDto
    {
        return $this->maintenanceDto;
    }

    /**
     * @param MaintenanceDto $maintenanceDto
     *
     * @return $this
     */
    public function setMaintenanceDto(MaintenanceDto $maintenanceDto): self
    {
        $this->maintenanceDto = $maintenanceDto;

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
     * @return Maintenance
     */
    public function getMaintenance(): Maintenance
    {
        return $this->maintenance;
    }

    /**
     * @param Maintenance $maintenance
     *
     * @return $this
     */
    public function setMaintenance(Maintenance $maintenance): self
    {
        $this->maintenance = $maintenance;

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
