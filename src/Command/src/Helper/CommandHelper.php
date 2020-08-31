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

class CommandHelper
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
     * @var string
     */
    private $srcDir;

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
        $this->srcDir = $kernelProjectDir.'/src';
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

        $this
            ->generateDto()
            ->generateForm()
            ->generateEvent()
            ->generateViewEvent()
            ->generateEventSubscriber()
            ->generateService()
            ->generateHydrator()
            ->generateTemplate()
            ->generateController()
            ->generateTranslation();

        $this->generator->writeChanges();
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function generateTranslation(): self
    {
        $className = strtolower($this->reflectionClass->getShortName());
        $path = $this->kernelProjectDir.'/translations/'.$className.'/'.$className.'.fr.yaml';

        $this->generator->generate(
            $path,
            'Translation.skeleton.yaml.twig',
            $this->getOptions()
        );

        return $this;
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
                'shortNamePlural' => $this->doctrineHelper->pluralize($this->reflectionClass->getShortName()),
                'properties' => $this->reflectionClass->getProperties(),
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
    private function generateController(): self
    {
        $className = $this->reflectionClass->getShortName();
        $path = $this->srcDir.'/Controller/'.$className.'Controller.php';

        $this->generator->generate(
            $path,
            'Controller.skeleton.php.twig',
            $this->getOptions()
        );

        return $this;
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function generateTemplate(): self
    {
        $className = strtolower($this->reflectionClass->getShortName());
        $path = $this->kernelProjectDir.'/templates/'.$className.'/';
        $options = $this->getOptions();

        $this->generator->generate(
            $path.'detail.html.twig',
            'detail.skeleton.php.twig',
            $options
        );

        $this->generator->generate(
            $path.'index.html.twig',
            'index.skeleton.php.twig',
            $options
        );

        $this->generator->generate(
            $path.'type.html.twig',
            'type.skeleton.php.twig',
            $options
        );

        return $this;
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function generateHydrator(): self
    {
        $className = $this->reflectionClass->getShortName();
        $path = $this->srcDir.'/Service/'.$className.'/'.$className.'HydratorService.php';

        $this->generator->generate(
            $path,
            'Hydrator.skeleton.php.twig',
            $this->getOptions()
        );

        return $this;
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function generateService(): self
    {
        $className = $this->reflectionClass->getShortName();
        $path = $this->srcDir.'/Service/'.$className.'/'.$className.'Service.php';

        $this->generator->generate(
            $path,
            'Service.skeleton.php.twig',
            $this->getOptions()
        );

        return $this;
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function generateEventSubscriber(): self
    {
        $className = $this->reflectionClass->getShortName();
        $path = $this->srcDir.'/EventSubscriber/'.$className.'EventsSubscriber.php';

        $this->generator->generate(
            $path,
            'EventSubscriber.skeleton.php.twig',
            $this->getOptions()
        );

        return $this;
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function generateViewEvent(): self
    {
        $className = $this->reflectionClass->getShortName();
        $path = $this->srcDir.'/Event/'.$className.'/'.$className.'ViewEvent.php';

        $this->generator->generate(
            $path,
            'ViewEvent.skeleton.php.twig',
            $this->getOptions()
        );

        return $this;
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function generateEvent(): self
    {
        $className = $this->reflectionClass->getShortName();
        $path = $this->srcDir.'/Event/'.$className.'/'.$className.'Event.php';

        $this->generator->generate($path, 'Event.skeleton.php.twig', $this->getOptions());

        return $this;
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function generateForm(): self
    {
        $className = $this->reflectionClass->getShortName();
        $path = $this->srcDir.'/Form/'.$className.'Type.php';

        $this->generator->generate($path, 'Form.skeleton.php.twig', $this->getOptions());

        return $this;
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function generateDto(): self
    {
        $className = $this->reflectionClass->getShortName();
        $path = $this->srcDir.'/Dto/'.$className.'/'.$className.'Dto.php';

        $this->generator->generate($path, 'Dto.skeleton.php.twig', $this->getOptions());

        return $this;
    }

    /**
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generateFunctionalTest(): self
    {
        $className = $this->reflectionClass->getShortName();
        $path = $this->kernelProjectDir.'/tests/Controller/'.$className.'ControllerTest.php';

        $this->generator->generate($path, 'FunctionalTest.skeleton.php.twig', $this->getOptions());

        $this->generator->writeChanges();

        return $this;
    }
}
