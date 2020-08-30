<?php

namespace App\DataFixtures;

use App\Entity\Language;
use App\Entity\Role;
use App\Entity\Translatable;
use App\Entity\Translation;
use App\Entity\User;
use App\Repository\LanguageRepository;
use App\Repository\RoleRepository;
use App\Service\UuidService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Yaml\Yaml;

class AppFixtures extends Fixture
{
    /**
     * @var UuidService
     */
    private $uuidService;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        UuidService $uuidService,
        LanguageRepository $languageRepository,
        RoleRepository $roleRepository,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->uuidService = $uuidService;
        $this->languageRepository = $languageRepository;
        $this->roleRepository = $roleRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        // Liste des noms de catégorie à ajouter
        $fixtures = Yaml::parseFile('src/DataFixtures/Data/fixtures.yaml');

        foreach ($fixtures as $entityType => $entities) {
            if (Language::class === $entityType) {
                $this->loadLanguage($entities);
            }
            if (Role::class === $entityType) {
                $this->loadRole($entities);
            }
            if (User::class === $entityType) {
                $this->loadUser($entities);
            }

            $manager->flush();
        }
    }

    public function loadLanguage(array $entities)
    {
        foreach ($entities as $entity) {
            $language = new Language();
            $language->setUuid($this->uuidService->create())
                ->setName($entity['name'])
                ->setAlternateName($entity['alternateName'])
                ->setDescription($entity['description'])
                ->setIso6391($entity['iso6391'])
                ->setIso6392B($entity['iso6392B'])
                ->setIso6392T($entity['iso6392T']);

            $this->manager->persist($language);
        }

        $this->manager->flush();
    }

    public function loadRole(array $entities)
    {
        foreach ($entities as $entity) {
            $role = new Role();
            $role->setUuid($this->uuidService->create())
                ->setName($entity['name']);

            $translatable = new Translatable();
            $translatable->setUuid($this->uuidService->create());

            foreach ($entity['translatable'] as $values) {
                $translation = new Translation();

                $language = $this->languageRepository->findOneBy(['iso6391' => $values['language']]);

                $translation->setUuid($this->uuidService->create())
                    ->setMessage($values['message'])
                    ->setLanguage($language);

                $translatable->addTranslation($translation);
            }

            $role->setTranslatable($translatable);

            $this->manager->persist($role);
            $this->manager->flush();
        }
    }

    public function loadUser(array $entities)
    {
        foreach ($entities as $entity) {
            $user = new User();

            $language = $this->languageRepository->findOneBy(['iso6391' => $entity['language']]);
            $role = $this->roleRepository->findOneBy(['name' => $entity['role']]);
            $roleUser = $this->roleRepository->findOneBy(['name' => 'ROLE_USER']);
            $password = $this->passwordEncoder->encodePassword($user, $entity['password']);

            $user->setUuid($this->uuidService->create())
                ->setEmail($entity['email'])
                ->setLanguage($language)
                ->addRole($role)
                ->addRole($roleUser)
                ->setPassword($password);

            $this->manager->persist($user);
        }
    }
}
