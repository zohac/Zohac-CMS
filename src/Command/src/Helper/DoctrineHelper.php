<?php

namespace App\Command\src\Helper;

use App\Command\src\Exception\CrudException;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
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

    private function isDoctrineInstalled(): bool
    {
        return null !== $this->registry;
    }

    /**
     * @param string|null $classOrNamespace
     *
     * @return array|\Doctrine\Persistence\Mapping\ClassMetadata
     *
     * @throws Exception
     */
    public function getMetadata(string $classOrNamespace = null)
    {
        $metadata = [];

        /** @var EntityManagerInterface $entityManager */
        foreach ($this->getRegistry()->getManagers() as $entityManager) {
            $metadataFactory = $entityManager->getMetadataFactory();

            foreach ($metadataFactory->getAllMetadata() as $m) {
                if (null === $classOrNamespace || 0 === strpos($m->getName(), $classOrNamespace)) {
                    $metadata[$m->getName()] = $m;
                }

                if ($m->getName() === $classOrNamespace) {
                    return $m;
                }
            }
        }

        return $metadata;
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

    public function isClassAMappedEntity(string $className): bool
    {
        if (!$this->isDoctrineInstalled()) {
            return false;
        }

        return (bool) $this->getMetadata($className);
    }
}
