<?php

namespace App\Command\src\Helper;

use App\Command\src\Service\Generator;
use Doctrine\Common\Inflector\Inflector as LegacyInflector;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Exception;
use ReflectionClass;
use ReflectionException;
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
     *
     * @return string
     */
    public function getPathForType(string $type): string
    {
        $path = null;
        $type = ucfirst($type);
        $className = $this->reflectionClass->getShortName();

        switch ($type) {
            case 'Controller':
                $path = $this->kernelProjectDir.'/src/'.$type.'/'.$className.$type.'.php';
                break;
            case 'EventSubscriber':
                $path = $this->kernelProjectDir.'/src/'.$type.'/'.$className.'EventsSubscriber.php';
                break;
            case 'Form':
                $path = $this->kernelProjectDir.'/src/'.$type.'/'.$className.'/'.$className.'Type.php';
                break;
            case 'ViewEvent':
                $path = $this->kernelProjectDir.'/src/Event/'.$className.'/'.$className.$type.'.php';
                break;
            case 'Hydrator':
                $path = $this->kernelProjectDir.'/src/Service/'.$className.'/'.$className.$type.'Service.php';
                break;
            case 'Template':
                $className = strtolower($className);
                $path = $this->kernelProjectDir.'/templates/'.$className.'/';
                break;
            case 'Translation':
                $className = strtolower($className);
                $path = $this->kernelProjectDir.'/translations/'.$className.'/'.$className.'.fr.yaml';
                break;
            default:
                $path = $this->kernelProjectDir.'/src/'.$type.'/'.$className.'/'.$className.$type.'.php';
                break;
        }

        if (!$path) {
            throw new RuntimeCommandException(
                sprintf('The file "%s" can\'t be generated because because the path cannot be null.', $className)
            );
        }

        return $path;
    }

    /**
     * @param string $type
     *
     * @return array|array[]
     */
    public function getOptionsForType(string $type): array
    {
        $options = [
            'entity' => [
                'shortName' => $this->reflectionClass->getShortName(),
                'shortNameToLower' => strtolower($this->reflectionClass->getShortName()),
            ],
        ];

        switch ($type) {
            case 'Template':
            case 'Controller':
                $options['entity']['shortNamePlural'] = $this->pluralize($this->reflectionClass->getShortName());
                $options['entity']['properties'] = $this->reflectionClass->getProperties();
                break;
            case 'Dto':
            case 'Form':
            case 'Hydrator':
            case 'Translation':
                $options['entity']['properties'] = $this->reflectionClass->getProperties();
                break;
        }

        return $options;
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
            ->generateForType('Dto')
            ->generateForType('Form')
            ->generateForType('Event')
            ->generateForType('ViewEvent')
            ->generateForType('EventSubscriber')
            ->generateForType('Service')
            ->generateForType('Hydrator')
            ->generateTemplate()
            ->generateForType('Controller')
            ->generateForType('Translation')
        ;

        $this->generator->writeChanges();
    }

    /**
     * @param string $type
     *
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generateForType(string $type): self
    {
        $type = ucfirst($type);
        $templatePath = ('Translation' === $type) ? $type.'.skeleton.yaml.twig' : $type.'.skeleton.php.twig';

        $this->generator->generate($this->getPathForType($type), $templatePath, $this->getOptionsForType($type));

        return $this;
    }

    /**
     * @return $this
     */
    public function generateTemplate(): self
    {
        $path = $this->getPathForType('Template');
        $options = $this->getOptionsForType('Template');

        $this->generator->generateTemplate(
            $path.'detail.htlm.twig',
            $this->templatePath.'/detail.skeleton.php',
            $options
        );

        $this->generator->generateTemplate(
            $path.'index.htlm.twig',
            $this->templatePath.'/index.skeleton.php',
            $options
        );

        $this->generator->generateTemplate(
            $path.'type.htlm.twig',
            $this->templatePath.'/type.skeleton.php',
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
