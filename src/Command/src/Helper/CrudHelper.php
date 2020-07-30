<?php

namespace App\Command\src\Helper;

use ReflectionException;
use Symfony\Bundle\MakerBundle\Doctrine\EntityDetails;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Inflector\Inflector;

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
     * @var string Class name only (without namespace)
     */
    protected $className;

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

    protected $projectDir;
    protected $skeletonDir;

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

        $ref = new \ReflectionClass($entityClass);
        $namespace = $ref->getNamespaceName();

        $this->namespacePrefix = substr($namespace, 0, strpos($namespace, '\Entity'));
        $this->className = $ref->getShortName();

        // generate (& fix) base route path
        $routePath = Str::asRoutePath($this->entityClass);
        $routePath = preg_replace('/(bundle|entity)\//', '', $routePath);

        // use setter to build also route name
        $this->setRoutePath($routePath);

        $this->buildClassDetails();

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
     * Generate files.
     *
     * @throws \Exception
     */
    public function generate()
    {
        if (!class_exists($this->entityClass)) {
            throw new \Exception('Invalid class name: '.$this->entityClass);
        }

        $this
            ->generateController()
//            ->generateForm()
//            ->generateTemplates()
//            ->generateEvent()
//            ->generateEventSubscriber()
//            ->generateDto()
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
        $this->generator->generateClass(
            $this->formClassDetails->getFullName(),
            $this->generateTemplatePath('form/Type.tpl.php'),
            array(
                'bounded_full_class_name' => $this->entityClassDetails->getFullName(),
                'bounded_class_name' => $this->entityClassDetails->getShortName(),
                'form_fields' => $this->entityDoctrineDetails->getFormFields(),
            )
        );

        $this->entityVarPlural = lcfirst(Inflector::pluralize($this->entityClassDetails->getShortName()));
        $this->entityVarSingular = lcfirst(Inflector::singularize($this->entityClassDetails->getShortName()));

        $this->entityTwigVarPlural = Str::asTwigVariable($this->entityVarPlural);
        $this->entityTwigVarSingular = Str::asTwigVariable($this->entityVarSingular);

        return $this;
    }

    /**
     * @return $this
     */
    protected function generateController()
    {
        $templatesPath = $this->bundleName.':'.$this->className;

        $repositoryVars = array(
            'repository_full_class_name' => $this->repositoryClassDetails->getFullName(),
            'repository_class_name' => $this->repositoryClassDetails->getShortName(),
            'repository_var' => lcfirst(Inflector::singularize($this->repositoryClassDetails->getShortName())),
        );

        $this->generator->generateController(
            $this->controllerClassDetails->getFullName(),
            $this->generateTemplatePath('crud/controller/Controller.tpl.php'),
            array_merge([
                'entity_full_class_name' => $this->entityClassDetails->getFullName(),
                'entity_class_name' => $this->entityClassDetails->getShortName(),
                'form_full_class_name' => $this->formClassDetails->getFullName(),
                'form_class_name' => $this->formClassDetails->getShortName(),
                'route_path' => $this->routePath,
                'route_name' => $this->routeName,
                'templates_path' => $templatesPath,
                'entity_var_plural' => $this->entityVarPlural,
                'entity_twig_var_plural' => $this->entityTwigVarPlural,
                'entity_var_singular' => $this->entityVarSingular,
                'entity_twig_var_singular' => $this->entityTwigVarSingular,
                'entity_identifier' => $this->entityDoctrineDetails->getIdentifier(),
                'format' => $this->format,
                'roles' => $this->roles,
            ],
                $repositoryVars
            )
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function generateTemplates()
    {
        // generate templates
        $templates = [
            '_delete_form' => [
                'route_name' => $this->routeName,
                'entity_twig_var_singular' => $this->entityTwigVarSingular,
                'entity_identifier' => $this->entityDoctrineDetails->getIdentifier(),
                'bundle_name' => $this->bundleName,
            ],
            '_form' => [],
            'edit' => [
                'entity_class_name' => $this->entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $this->entityTwigVarSingular,
                'entity_identifier' => $this->entityDoctrineDetails->getIdentifier(),
                'bundle_name' => $this->bundleName,
                'route_name' => $this->routeName,
            ],
            'index' => [
                'entity_class_name' => $this->entityClassDetails->getShortName(),
                'entity_twig_var_plural' => $this->entityTwigVarPlural,
                'entity_twig_var_singular' => $this->entityTwigVarSingular,
                'entity_identifier' => $this->entityDoctrineDetails->getIdentifier(),
                'entity_fields' => $this->entityDoctrineDetails->getDisplayFields(),
                'bundle_name' => $this->bundleName,
                'route_name' => $this->routeName,
            ],
            'new' => [
                'entity_class_name' => $this->entityClassDetails->getShortName(),
                'bundle_name' => $this->bundleName,
                'route_name' => $this->routeName,
            ],
            'show' => [
                'entity_class_name' => $this->entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $this->entityTwigVarSingular,
                'entity_identifier' => $this->entityDoctrineDetails->getIdentifier(),
                'entity_fields' => $this->entityDoctrineDetails->getDisplayFields(),
                'bundle_name' => $this->bundleName,
                'route_name' => $this->routeName,
            ],
        ];

        foreach ($templates as $template => $variables) {
            $filePath = sprintf('%s/Resources/views/%s/%s.html.twig', $this->bundleDir, $this->className, $template);
            $templatePath = $this->generateTemplatePath('crud/templates/'.$template.'.tpl.php');

            $this->generator->generateFile($filePath, $templatePath, $variables);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function generateRoutingFiles()
    {
        if ('annotation' !== $this->format) {
            // generate crud routing file
            $crudRoutingFilePath = sprintf('%s/Resources/config/routing/%s.%s', $this->bundleDir, $this->entityTwigVarSingular, $this->format);
            $crudRoutingTemplatePath = $this->generateTemplatePath(sprintf('crud/config/routing-crud.%s.tpl.php', $this->format));

            $crudRoutingVars = array(
                'bundle_name' => $this->bundleName,
                'entity' => $this->className,
                'route_name' => $this->routeName,
                'entity_identifier' => $this->entityDoctrineDetails->getIdentifier(),
                'format' => $this->format,
            );
            $this->generator->generateFile($crudRoutingFilePath, $crudRoutingTemplatePath, $crudRoutingVars);

            // generate or append to include file
            $routingFilePath = sprintf('%s/Resources/config/routing.%s', $this->bundleDir, $this->format);
            $routingTemplatePath = $this->generateTemplatePath(sprintf('crud/config/routing.%s.tpl.php', $this->format));

            $routeNameInclude = sprintf('%s_%s', $this->routeName, $this->routeName);

            $routingVars = array(
                'bundle_name' => $this->bundleName,
                'route_name' => $this->routeName,
                'route_path' => $this->routePath,
                'route_name_include' => $routeNameInclude,
                'entity_twig_var_singular' => $this->entityTwigVarSingular,
                'format' => $this->format,
            );

            // file exists... append
            if (!file_exists($routingFilePath)) {
                $this->generator->generateFile($routingFilePath, $routingTemplatePath, $routingVars);
            } else {
                $existingContent = file_get_contents($routingFilePath);

                // prepend route include to file
                if (false === strpos($existingContent, $routeNameInclude)) {
                    $parsedContent = $this->fileManager->parseTemplate($routingTemplatePath, $routingVars);

                    $newContent = $parsedContent."\r\n\r\n".$existingContent;

                    $this->fileManager->dumpFile($routingFilePath, $newContent);
                }
            }
        }

        return $this;
    }

    /**
     * Generate template file path.
     *
     * @param $templateName
     *
     * @return string
     */
    protected function generateTemplatePath($templateName)
    {
        return $this->skeletonDir.'/'.$templateName;
    }
}
