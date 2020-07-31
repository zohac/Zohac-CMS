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
     * @param array $options
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
     * @param string $className
     * @param string $templatePath
     * @param array $options
     * @return $this
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function generateDto(string $className, string $templatePath, array $options = [])
    {
        $path = $this->kernelProjectDir.'/src/Dto/'.$className.'/'.$className.'Dto.php';

        $template = $this->render($templatePath, $options);

        $this->addOperation($path, $template);

        return $this;
    }

    /**
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
    public function generateController(string $className, string $templatePath, array $options = [])
    {
        $path = $this->kernelProjectDir.'/src/Controller/'.$className.'Controller.php';

        $template = $this->render($templatePath, $options);

        $this->addOperation($path, $template);

        return $this;
    }

    public function addOperation(string $path, string $template)
    {
        if ($this->fileManager->exists($path)) {
            throw new RuntimeCommandException(
                sprintf('The file "%s" can\'t be generated because it already exists.',
                    $this->fileManager->makePathRelative($path, $this->kernelProjectDir))
            );
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
