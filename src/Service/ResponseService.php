<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error as TwigError;

class ResponseService
{
    /**
     * @var Environment
     */
    private $twigEnvironment;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(Environment $twigEnvironment, RouterInterface $router)
    {
        $this->twigEnvironment = $twigEnvironment;
        $this->router = $router;
    }

    /**
     * @param string|null $view
     * @param array       $options
     *
     * @return Response
     *
     * @throws TwigError\LoaderError
     * @throws TwigError\RuntimeError
     * @throws TwigError\SyntaxError
     */
    public function getResponse(?string $view = null, array $options = [])
    {
        $response = new Response();

        $content = $this->twigEnvironment->render($view, $options);

        $response->setContent($content);

        return $response;
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url
     * @param int    $status
     *
     * @return RedirectResponse
     */
    public function redirect(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param string $route
     * @param array  $parameters
     * @param int    $status
     *
     * @return RedirectResponse
     */
    public function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @see UrlGeneratorInterface
     *
     * @param string $route
     * @param array  $parameters
     * @param int    $referenceType
     *
     * @return string
     */
    protected function generateUrl(
        string $route,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        return $this->router->generate($route, $parameters, $referenceType);
    }
}
