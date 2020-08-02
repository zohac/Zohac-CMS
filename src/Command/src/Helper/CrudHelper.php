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
     * @return array
     */
    public function getEntitiesForAutocomplete()
    {
        return $this->doctrineHelper->getEntitiesForAutocomplete();
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
//            ->generateDto()
//            ->generateForm()
//            ->generateEvent()
            ->generateViewEvent()
//            ->generateEventSubscriber()
//            ->generateService()
//            ->generateHydrator()
//            ->generateTemplates()
//            ->generateController()
        ;

        $this->generator->writeChanges();
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function generateForm()
    {
        $this->generator->generate(
            'Form',
            $this->reflectionClass->getShortName(),
            'Form.skeleton.php.twig',
            [
                'entity' => [
                    'shortName' => $this->reflectionClass->getShortName(),
                    'shortNameToLower' => strtolower($this->reflectionClass->getShortName()),
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
    public function generateDto()
    {
        $this->generator->generate(
            'Dto',
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
    protected function generateEvent()
    {
        $this->generator->generate(
            'Event',
            $this->reflectionClass->getShortName(),
            'Event.skeleton.php.twig',
            [
                'entity' => [
                    'shortName' => $this->reflectionClass->getShortName(),
                    'shortNameToLower' => strtolower($this->reflectionClass->getShortName()),
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
    protected function generateViewEvent()
    {
        $this->generator->generate(
            'ViewEvent',
            $this->reflectionClass->getShortName(),
            'ViewEvent.skeleton.php.twig',
            [
                'entity' => [
                    'shortName' => $this->reflectionClass->getShortName(),
                    'shortNameToLower' => strtolower($this->reflectionClass->getShortName()),
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
        $this->generator->generate(
            'Controller',
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
