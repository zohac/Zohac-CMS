<?php

namespace App\Command\src\Helper;

use App\Command\src\Exception\CrudException;
use App\Command\src\Service\Generator;
use Exception;
use ReflectionClass;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class FunctionalTestHelper
{
    /**
     * @var DoctrineHelper
     */
    protected $doctrineHelper;

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

    /**
     * @var string
     */
    private $kernelProjectDir;

    /**
     * CrudHelper constructor.
     *
     * @param DoctrineHelper $doctrineHelper
     * @param Generator      $generator
     * @param string         $kernelProjectDir
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        Generator $generator,
        string $kernelProjectDir
    ) {
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
     *
     * @throws Exception
     */
    public function getEntitiesForAutocomplete()
    {
        return $this->doctrineHelper->getEntitiesForAutocomplete();
    }

    /**
     * @throws CrudException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generate()
    {
        if (!class_exists($this->entityClass)) {
            throw new CrudException('Invalid class name: '.$this->entityClass);
        }

        $this->generateFunctionalTest();

        $this->generator->writeChanges();
    }

    /**
     * @return array|array[]
     */
    public function getOptions(): array
    {
        return [
            'entity' => [
                'shortName' => $this->reflectionClass->getShortName(),
                'shortNameToLower' => strtolower($this->reflectionClass->getShortName()),
            ],
        ];
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function generateFunctionalTest(): self
    {
        $className = $this->reflectionClass->getShortName();
        $path = $this->kernelProjectDir.'/tests/Controller/'.$className.'ControllerTest.php';

        $this->generator->generate($path, 'FunctionalTest.skeleton.php.twig', $this->getOptions());

        return $this;
    }
}
