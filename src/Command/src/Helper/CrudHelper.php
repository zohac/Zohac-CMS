<?php

namespace App\Command\src\Helper;

use App\Command\src\Service\Generator;
use ReflectionClass;
use ReflectionException;
use Symfony\Bundle\MakerBundle\Doctrine\EntityDetails;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CrudHelper
{
    /**
     * @var DoctrineHelper
     */
    protected $doctrineHelper;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @var string Full class name (with namespace)
     */
    protected $entityClass;
    protected $routePath;
    protected $routeName;

    protected $bundleName;
    protected $bundleDir;
    protected $namespacePrefix;

    /**
     * @var ClassNameDetails
     */
    protected $repositoryClassDetails;

    /**
     * @var ClassNameDetails
     */
    protected $entityClassDetails;

    /**
     * @var ClassNameDetails
     */
    protected $formClassDetails;

    /**
     * @var ClassNameDetails
     */
    protected $controllerClassDetails;

    /**
     * @var EntityDetails
     */
    protected $entityDoctrineDetails;

    protected $entityVarPlural;
    protected $entityVarSingular;
    protected $entityTwigVarPlural;
    protected $entityTwigVarSingular;

    /**
     * CrudHelper constructor.
     *
     * @param DoctrineHelper $doctrineHelper
     * @param Generator      $generator
     */
    public function __construct(DoctrineHelper $doctrineHelper, Generator $generator)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->generator = $generator;
    }

    /**
     * @param SymfonyStyle $io
     *
     * @return $this
     */
    public function setIO(SymfonyStyle $io)
    {
        $this->io = $io;
//        $this->fileManager->setIO($io);

        return $this;
    }

    /**
     * @param $entityClass
     *
     * @return $this
     *
     * @throws ReflectionException
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        $this->reflectionClass = new ReflectionClass($entityClass);

        return $this;
    }

    /**
     * @param $routePath
     *
     * @return $this
     */
    public function setRoutePath($routePath)
    {
        $this->routePath = $routePath;

        // generate (& fix) base route name
        $this->routeName = Str::asRouteName(trim($this->routePath, '/'));

        return $this;
    }

    /**
     * @return array
     */
    public function getEntitiesForAutocomplete()
    {
        return $this->doctrineHelper->getEntitiesForAutocomplete();
    }

    /**
     * @return mixed
     */
    public function getRoutePath()
    {
        return $this->routePath;
    }

    protected function buildClassDetails()
    {
        // create entity class details
        $this->entityClassDetails = new ClassNameDetails($this->entityClass, $this->namespacePrefix.'\\Entity\\', '');
        $this->entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($this->entityClassDetails->getFullName());

        // create form class details
        $formClass = sprintf('%s\\Form\\%sType', $this->namespacePrefix, $this->className);
        $this->formClassDetails = new ClassNameDetails($formClass, $this->namespacePrefix.'\\Form\\', 'Type');

        // create repository class details
        $repositoryClass = sprintf('%s\\Repository\\%sRepository', $this->namespacePrefix, $this->className);
        $this->repositoryClassDetails = new ClassNameDetails($repositoryClass, $this->namespacePrefix.'\\Repository\\', 'Repository');

        // create controller class details
        $controllerClass = sprintf('%s\\Controller\\%sController', $this->namespacePrefix, $this->className);
        $this->controllerClassDetails = new ClassNameDetails($controllerClass, $this->namespacePrefix.'\\Controller\\', 'Controller');
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generate()
    {
        if (!class_exists($this->entityClass)) {
            throw new \Exception('Invalid class name: '.$this->entityClass);
        }

        $this
            ->generateDto()
//            ->generateController()
//            ->generateForm()
//            ->generateTemplates()
//            ->generateEvent()
//            ->generateEventSubscriber()
//            ->generateService()
//            ->generateHydrator()
        ;

        $this->generator->writeChanges();
    }

    /**
     * @return $this
     */
    protected function generateForm()
    {
        return $this;
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generateDto()
    {
        $this->generator->generateDto(
            $this->reflectionClass->getShortName(),
            'Dto.skeleton.php.twig',
            [
                'entity' => [
                    'shortName' => $this->reflectionClass->getShortName(),
                    'properties' => $this->reflectionClass->getProperties(),
                ],
            ]);

        return $this;
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function generateController()
    {
        $this->generator->generateController(
            $this->reflectionClass->getShortName(),
            'Controller.skeleton.php.twig',
            [
                'entity' => [
                        'shortName' => $this->reflectionClass->getShortName(),
                        'shortNameToLower' => strtolower($this->reflectionClass->getShortName()),
                    ],
            ]);

        return $this;
    }
}
