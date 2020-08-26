<?php

namespace App\Service\Translation;

use App\Dto\Translation\TranslationDto;
use App\Entity\Language;
use App\Entity\Translation;
use App\Exception\UuidException;
use App\Interfaces\Dto\DtoInterface;
use App\Interfaces\EntityInterface;
use App\Interfaces\Service\EntityHydratorInterface;
use App\Repository\LanguageRepository;
use App\Service\UuidService;

class TranslationHydratorService implements EntityHydratorInterface
{
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * TranslationHydratorService constructor.
     *
     * @param UuidService        $uuidService
     * @param LanguageRepository $languageRepository
     */
    public function __construct(UuidService $uuidService, LanguageRepository $languageRepository)
    {
        $this->uuidService = $uuidService;
        $this->languageRepository = $languageRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UuidException
     */
    public function hydrateEntityWithDto(EntityInterface $entity, DtoInterface $dto): EntityInterface
    {
        /* @var Translation $entity */
        /* @var TranslationDto $dto */
        return $this->getHydratedEntity(
            $entity,
            $this->getUuid($dto->uuid),
            $this->getLanguage($dto->language),
            $dto->message
        );
    }

    /**
     * @param Translation $entity
     * @param string      $uuid
     * @param Language    $language
     * @param string      $message
     *
     * @return EntityInterface
     */
    public function getHydratedEntity(
        Translation $entity,
        string $uuid,
        Language $language,
        string $message
    ): EntityInterface {
        return $entity->setUuid($uuid)
            ->setLanguage($language)
            ->setMessage($message);
    }

    /**
     * {@inheritdoc}
     *
     * @throws UuidException
     */
    public function getUuid(?string $uuid = null): string
    {
        $uuid = (null !== $uuid) ? $uuid : $this->uuidService->create();

        return $uuid;
    }

    /**
     * @param string $languageUuid
     *
     * @return Language
     */
    public function getLanguage(string $languageUuid): Language
    {
        return $this->languageRepository->findOneBy(['uuid' => $languageUuid]);
    }

    /**
     * @param EntityInterface $entity
     * @param array           $values
     *
     * @return EntityInterface
     *
     * @throws UuidException
     */
    public function hydrateEntityWithArray(EntityInterface $entity, array $values): EntityInterface
    {
        /* @var Translation $entity */
        return $this->getHydratedEntity(
            $entity,
            $this->getUuid(),
            $this->getLanguage($values['language']),
            $values['message']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateDtoWithEntity(EntityInterface $entity, DtoInterface $dto): DtoInterface
    {
        /* @var Translation $entity */
        /* @var TranslationDto $dto */
        $dto->uuid = $entity->getUuid();
        $dto->language = $entity->getLanguage()->getUuid();
        $dto->message = $entity->getMessage();

        return $dto;
    }

    /**
     * {@inheritdoc}
     */
    public function canHandle(EntityInterface $entity): bool
    {
        return $entity instanceof Language;
    }
}
