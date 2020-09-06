<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HttpFondationService
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        UrlGeneratorInterface $router,
        SessionInterface $session
    ) {
        $this->router = $router;
        $this->session = $session;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getRouter(): UrlGeneratorInterface
    {
        return $this->router;
    }
}
