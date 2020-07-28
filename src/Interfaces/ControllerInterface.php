<?php

namespace App\Interfaces;

use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\Event\ViewEventInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ControllerInterface
{
    /**
     * @param string $eventName
     *
     * @return ViewEventInterface
     */
    public function getViewEvent(string $eventName): string;

    /**
     * @param string $eventName
     *
     * @return ViewEventInterface
     */
    public function getEvent(string $eventName): string;

    /**
     * @param ServiceEntityRepositoryInterface $repository
     * @param string                           $entity            the class name (Entity::class)
     * @param array|null                       $repositoryOptions
     *
     * @return Response
     */
    public function index(
        ServiceEntityRepositoryInterface $repository,
        string $entity,
        ?array $repositoryOptions = []
    ): Response;

    /**
     * @param EntityInterface|null $entity
     *
     * @return Response
     */
    public function show(?EntityInterface $entity = null): Response;

    /**
     * @param Request      $request
     * @param DtoInterface $dto
     * @param string       $entity
     * @param string       $formType
     *
     * @return Response
     */
    public function new(Request $request, DtoInterface $dto, string $entity, string $formType): Response;

    /**
     * @param Request         $request
     * @param EntityInterface $entity
     * @param string          $formType
     *
     * @return Response
     */
    public function edit(Request $request, EntityInterface $entity, string $formType): Response;

    /**
     * @param Request         $request
     * @param EntityInterface $entity
     * @param string|null     $option
     *
     * @return Response
     */
    public function delete(Request $request, EntityInterface $entity, ?string $option = null): Response;
}
