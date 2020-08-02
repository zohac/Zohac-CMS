<?php

namespace App\Command\src\Service;

use App\Command\src\Helper\TwigHelper;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Generator
{
    /**
     * @var TwigHelper
     */
    private $twigHelper;

    /**
     * @var Filesystem
     */
    private $fileManager;

    /**
     * @var array
     */
    private $pendingOperations = [];

    /**
     * @var string
     */
    private $kernelProjectDir;

    /**
     * Generator constructor.
     *
     * @param TwigHelper $twigHelper
     * @param Filesystem $fileManager
     * @param string     $kernelProjectDir
     */
    public function __construct(TwigHelper $twigHelper, Filesystem $fileManager, string $kernelProjectDir)
    {
        $this->twigHelper = $twigHelper;
        $this->fileManager = $fileManager;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * @param string $templatePath
     * @param array  $options
     *
     * @return string
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function render(string $templatePath, array $options = []): string
    {
        return $this->twigHelper->render($templatePath, $options);
    }

    /**
     * @param string $type
     * @param string $className
     * @param string $templatePath
     * @param array  $options
     *
     * @return $this
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generate(string $type, string $className, string $templatePath, array $options = [])
    {
        if ($path = $this->getPathForType($type, $className)) {
            $template = $this->render($templatePath, $options);

            $this->addOperation($path, $template);

            return $this;
        }

        throw new RuntimeCommandException(sprintf('The file "%s" can\'t be generated because because the path cannot be null.', $className));
    }

    /**
     * @param string $type
     * @param string $classeName
     *
     * @return string|null
     */
    public function getPathForType(string $type, string $classeName): ?string
    {
        $path = null;
        $type = ucfirst($type);

        switch ($type) {
            case 'Controller':
                $path = $this->kernelProjectDir.'/src/'.$type.'/'.$classeName.$type.'.php';
                break;
            case 'Form':
                $path = $this->kernelProjectDir.'/src/'.$type.'/'.$classeName.'/'.$classeName.'Type.php';
                break;
            case 'ViewEvent':
                $path = $this->kernelProjectDir.'/src/Event/'.$classeName.'/'.$classeName.$type.'.php';
                break;
            default:
                $path = $this->kernelProjectDir.'/src/'.$type.'/'.$classeName.'/'.$classeName.$type.'.php';
                break;
        }

        return $path;
    }

    public function addOperation(string $path, string $template)
    {
        if ($this->fileManager->exists($path)) {
            throw new RuntimeCommandException(sprintf('The file "%s" can\'t be generated because it already exists.', $this->fileManager->makePathRelative($path, $this->kernelProjectDir)));
        }

        $this->pendingOperations[$path] = $template;

        return $this;
    }

    public function writeChanges()
    {
        foreach ($this->pendingOperations as $className => $operation) {
            $this->fileManager->dumpFile($className, $operation);
        }

        return $this;
    }
}
