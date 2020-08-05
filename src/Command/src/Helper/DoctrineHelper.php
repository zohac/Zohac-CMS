<?php

namespace App\Command\src\Helper;

use App\Command\src\Exception\CrudException;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\MakerBundle\Doctrine\EntityDetails;

/**
 * Class DoctrineHelper.
 */
class DoctrineHelper
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var ClassMetadata[]
     */
    private $metadataBag = [];

    /**
     * DoctrineHelper constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function getEntitiesForAutocomplete(): array
    {
        $entities = [];

        if ($this->isDoctrineInstalled()) {
            $allMetadata = $this->getMetadata();

            /* @var ClassMetadata $metadata */
            foreach (array_keys($allMetadata) as $classname) {
                $entities[] = $classname;
            }
        }

        return $entities;
    }

    /**
     * @return bool
     */
    private function isDoctrineInstalled(): bool
    {
        return null !== $this->registry;
    }

    /**
     * @param string|null $classOrNamespace
     *
     * @return ClassMetadata[]|ClassMetadata
     *
     * @throws Exception
     */
    public function getMetadata(string $classOrNamespace = null)
    {
        $this->metadataBag = [];

        /** @var EntityManagerInterface $entityManager */
        foreach ($this->getRegistry()->getManagers() as $entityManager) {
            $metadataFactory = $entityManager->getMetadataFactory();

            if ($metadata = $this->parseAllMetadata($metadataFactory, $classOrNamespace)) {
                return $metadata;
            }
        }

        return $this->metadataBag;
    }

    /**
     * @param ClassMetadataFactory $metadataFactory
     * @param string|null          $classOrNamespace
     *
     * @return ClassMetadata|null
     */
    private function parseAllMetadata(ClassMetadataFactory $metadataFactory, ?string $classOrNamespace): ?ClassMetadata
    {
        foreach ($metadataFactory->getAllMetadata() as $metadata) {
            $this->sortMetadata($metadata, $classOrNamespace);

            if ($metadata->getName() === $classOrNamespace) {
                return $metadata;
            }
        }

        return null;
    }

    /**
     * @param ClassMetadata $metadata
     * @param string|null $classOrNamespace
     */
    private function sortMetadata(ClassMetadata $metadata, ?string $classOrNamespace): void
    {
        if (null === $classOrNamespace || 0 === strpos($metadata->getName(), $classOrNamespace)) {
            $this->metadataBag[$metadata->getName()] = $metadata;
        }
    }

    /**
     * @return ManagerRegistry
     *
     * @throws CrudException
     */
    public function getRegistry(): ManagerRegistry
    {
        // this should never happen: we will have checked for the
        // DoctrineBundle dependency before calling this
        if (null === $this->registry) {
            throw new CrudException('Somehow the doctrine service is missing. Is DoctrineBundle installed?');
        }

        return $this->registry;
    }

    /**
     * @param string $entityClassName
     *
     * @return EntityDetails|null
     *
     * @throws Exception
     */
    public function createDoctrineDetails(string $entityClassName)
    {
        $metadata = $this->getMetadata($entityClassName);

        if ($metadata instanceof ClassMetadata) {
            return new EntityDetails($metadata);
        }

        return null;
    }

    /**
     * @param string $className
     *
     * @return bool
     *
     * @throws Exception
     */
    public function isClassAMappedEntity(string $className): bool
    {
        if (!$this->isDoctrineInstalled()) {
            return false;
        }

        return (bool) $this->getMetadata($className);
    }
}
