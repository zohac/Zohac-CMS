<?php

namespace App\Command\src\Helper;

use App\Command\src\Service\Generator;
use Doctrine\Common\Inflector\Inflector as LegacyInflector;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Exception;
use ReflectionClass;
use ReflectionException;
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
     * @var string
     */
    private $templatePath;

    /**
     * @var Inflector
     */
    private $inflector;

    /**
     * CrudHelper constructor.
     *
     * @param DoctrineHelper $doctrineHelper
     * @param Generator      $generator
     * @param string         $kernelProjectDir
     * @param string         $templatePath
     */
    public function __construct(
        DoctrineHelper $doctrineHelper,
        Generator $generator,
        string $kernelProjectDir,
        string $templatePath
    ) {
        $this->doctrineHelper = $doctrineHelper;
        $this->generator = $generator;
        $this->kernelProjectDir = $kernelProjectDir;
        $this->templatePath = $templatePath;

        if (class_exists(InflectorFactory::class)) {
            $this->inflector = InflectorFactory::create()->build();
        }

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
     * @return array|array[]
     */
    public function getOptions(): array
    {
        return [
            'entity' => [
                'shortName' => $this->reflectionClass->getShortName(),
                'shortNameToLower' => strtolower($this->reflectionClass->getShortName()),
                'shortNamePlural' => $this->pluralize($this->reflectionClass->getShortName()),
                'properties' => $this->reflectionClass->getProperties(),
            ],
        ];
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
            ->generateDto()
            ->generateForm()
            ->generateEvent()
            ->generateViewEvent()
            ->generateEventSubscriber()
            ->generateService()
            ->generateHydrator()
            ->generateTemplate()
            ->generateController()
            ->generateTranslation()
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
    public function generateDto(): self
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
    public function generateForm(): self
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
    public function generateEvent(): self
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
    public function generateViewEvent(): self
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
    public function generateEventSubscriber(): self
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
    public function generateService(): self
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
    public function generateHydrator(): self
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
    public function generateController(): self
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
    public function generateTranslation(): self
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
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generateTemplate(): self
    {
        $className = strtolower($this->reflectionClass->getShortName());
        $path = $this->kernelProjectDir.'/templates/'.$className.'/';
        $options = $this->getOptions();

        $this->generator->generate(
            $path.'detail.htlm.twig',
            'detail.skeleton.php.twig',
            $options
        );

        $this->generator->generate(
            $path.'index.htlm.twig',
            'index.skeleton.php.twig',
            $options
        );

        $this->generator->generate(
            $path.'type.htlm.twig',
            'type.skeleton.php.twig',
            $options
        );

        return $this;
    }

    private function pluralize(string $word): string
    {
        $word = strtolower($word);

        if (null !== $this->inflector) {
            return $this->inflector->pluralize($word);
        }

        return LegacyInflector::pluralize($word);
    }
}
