<?php

namespace App\Service\Parameter;

use App\Dto\Parameter\ParameterDto;
use App\Entity\Parameter;
use App\Event\Parameter\ParameterEvent;
use App\Exception\HydratorException;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\ServiceInterface;
use App\Service\EntityService;
use App\Service\EventService;
use App\Service\FlashBagService;
use ReflectionException;

class ParameterService implements ServiceInterface
{
    /**
     * @var EventService
     */
    private $eventService;

    /**
     * @var FlashBagService
     */
    private $flashBagService;

    /**
     * @var EntityService
     */
    private $entityService;

    /**
     * ParameterService constructor.
     *
     * @param EventService    $eventService
     * @param FlashBagService $flashBagService
     * @param EntityService   $entityService
     */
    public function __construct(
        EventService $eventService,
        FlashBagService $flashBagService,
        EntityService $entityService
    ) {
        $this->eventService = $eventService;
        $this->flashBagService = $flashBagService;
        $this->entityService = $entityService;
    }

    /**
    * @param ParameterDto $parameterDto
    *
    * @return Parameter
    *
    * @throws HydratorException
    */
    public function createParameterFromDto(ParameterDto $parameterDto): Parameter
    {
        /** @var Parameter $parameter */
        $parameter = $this->entityService->hydrateEntityWithDto(new Parameter(), $parameterDto);

        $this->eventService->dispatchEvent(ParameterEvent::POST_CREATE, [
            'parameter' => $parameter,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            'Parameter',
            'Parameter successfully created.',
            'parameter'
        );

        return $parameter;
    }

    /**
    * @param ParameterDto $parameterDto
    * @param Parameter    $parameter
    *
    * @return Parameter
    *
    * @throws HydratorException
    */
    public function updateParameterFromDto(ParameterDto $parameterDto, Parameter $parameter): Parameter
    {
        /** @var Parameter $parameter */
        $parameter = $this->entityService->hydrateEntityWithDto($parameter, $parameterDto);

        $this->eventService->dispatchEvent(ParameterEvent::POST_UPDATE, [
            'parameter' => $parameter,
        ]);

        $this->flashBagService->addAndTransFlashMessage(
            'Parameter',
            'Parameter successfully updated.',
            'parameter'
        );

        return $parameter;
    }

    /**
    * @param Parameter $parameter
    *
    * @return $this
    *
    * @throws ReflectionException
    */
    public function deleteParameter(Parameter $parameter): self
    {
        $this->entityService
            ->setEntity($parameter)
            ->remove($parameter)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'Parameter',
            'Parameter successfully deleted.',
            $this->entityService->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(ParameterEvent::POST_DELETE);

        return $this;
    }

    /**
    * @param Parameter $parameter
    *
    * @return $this
    *
    * @throws ReflectionException
    */
    public function deleteSoftParameter(Parameter $parameter)
    {
        $parameter->setArchived(true);

        $this->entityService
            ->setEntity($parameter)
            ->persist($parameter)
            ->flush();

        $this->flashBagService->addAndTransFlashMessage(
            'Parameter',
            'Parameter successfully deleted.',
            $this->entityService->getEntityNameToLower()
        );

        $this->eventService->dispatchEvent(ParameterEvent::POST_DELETE);

        return $this;
    }

    /**
    * @param EntityInterface $entity
    *
    * @return string
    */
    public function getDeleteMessage(EntityInterface $entity): string
    {
        // TODO: Change the Delete Message

        /* @var Parameter $entity */
        //  return $this->flashBagService->trans(
        //      'Are you sure you want to delete this parameter (%parameter%) ?',
        //      'parameter',
        //      ['parameter' => $entity->getName()]
        //  );

        return 'Are you sure you want to delete this parameter  ?';
    }
}
