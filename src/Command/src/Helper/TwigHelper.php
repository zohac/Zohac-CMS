<?php

namespace App\Command\src\Helper;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\TemplateWrapper;

/**
 * Class TwigEnvironmentService.
 */
class TwigHelper
{
    /**
     * @var Environment
     */
    private $twigEnvironment;

    /**
     * TwigEnvironmentService constructor.
     *
     * @param Environment $twigEnvironment
     */
    public function __construct(Environment $twigEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * Renders a template.
     *
     * @param string $template
     * @param array $options An array of parameters to pass to the template
     * @return string
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(string $template, array $options = []): string
    {
        return $this->twigEnvironment->render($template ,$options);
    }
}
