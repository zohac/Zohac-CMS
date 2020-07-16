<?php

namespace App\Interfaces;

use App\Interfaces\Dto\DtoInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ControllerInterface
{
    /**
     * @param ServiceEntityRepositoryInterface $repository
     * @param string                           $entity     the class name (Entity::class)
     *
     * @return Response
     */
    public function index(ServiceEntityRepositoryInterface $repository, string $entity): Response;

    /**
     * @param ServiceEntityRepositoryInterface $repository
     * @param string                           $entity     the class name (Entity::class)
     *
     * @return Response
     */
    public function softIndex(ServiceEntityRepositoryInterface $repository, string $entity): Response;

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
     *
     * @return Response
     */
    public function delete(Request $request, EntityInterface $entity): Response;

    /**
     * @param Request         $request
     * @param EntityInterface $entity
     *
     * @return Response
     */
    public function softDelete(Request $request, EntityInterface $entity): Response;
}
