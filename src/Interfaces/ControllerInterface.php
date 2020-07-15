<?php

namespace App\Interfaces;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

interface ControllerInterface
{
    /**
     * @param ServiceEntityRepositoryInterface $repository
     * @param string                           $entity     the class name (Entity::class)
     * @param bool                             $soft
     *
     * @return Response
     */
    public function index(ServiceEntityRepositoryInterface $repository, string $entity, bool $soft = false): Response;

    /**
     * @param EntityInterface|null $entity
     *
     * @return Response
     */
    public function show(?EntityInterface $entity = null): Response;
}
