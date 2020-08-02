<?php

namespace App\Command\src\Helper;

use App\Command\src\Service\Generator;
use Exception;
use ReflectionClass;
use ReflectionException;
use Symfony\Bundle\MakerBundle\Doctrine\EntityDetails;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
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
     * @var string
     */
    private $kernelProjectDir;

    /**
     * CrudHelper constructor.
     *
     * @param DoctrineHelper $doctrineHelper
     * @param Generator      $generator
     */
    public function __construct(DoctrineHelper $doctrineHelper, Generator $generator, string $kernelProjectDir)
    {
        $this->doctrineHelper = $doctrineHelper;
        $this->generator = $generator;
        $this->kernelProjectDir = $kernelProjectDir;
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
     * @param string $type
     * @param string $className
     * @return string
     */
    public function getPathForType(string $type, string $className): string
    {
        $path = null;
        $type = ucfirst($type);

        switch ($type) {
            case 'Controller':
                $path = $this->kernelProjectDir.'/src/'.$type.'/'.$className.$type.'.php';
                break;
            case 'Form':
                $path = $this->kernelProjectDir.'/src/'.$type.'/'.$className.'/'.$className.'Type.php';
                break;
            case 'ViewEvent':
                $path = $this->kernelProjectDir.'/src/Event/'.$className.'/'.$className.$type.'.php';
                break;
            default:
                $path = $this->kernelProjectDir.'/src/'.$type.'/'.$className.'/'.$className.$type.'.php';
                break;
        }

        if (! $path) {
            throw new RuntimeCommandException(
                sprintf('The file "%s" can\'t be generated because because the path cannot be null.', $className)
            );
        }

        return $path;
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    public function generate()
    {
        if (!class_exists($this->entityClass)) {
            throw new Exception('Invalid class name: '.$this->entityClass);
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
            $this->getPathForType('Form', $this->reflectionClass->getShortName()),
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
            $this->getPathForType('Dto', $this->reflectionClass->getShortName()),
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
            $this->getPathForType('Event', $this->reflectionClass->getShortName()),
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
            $this->getPathForType('ViewEvent', $this->reflectionClass->getShortName()),
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
            $this->getPathForType('Controller', $this->reflectionClass->getShortName()),
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
