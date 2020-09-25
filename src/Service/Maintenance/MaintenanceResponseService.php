<?php

namespace App\Service\Maintenance;

use App\Service\ResponseService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error as TwigError;

class MaintenanceResponseService
{
    /**
     * @var ResponseService
     */
    private $responseService;

    /**
     * @var MaintenanceService
     */
    private $maintenanceService;

    public function __construct(ResponseService $responseService, MaintenanceService $maintenanceService)
    {
        $this->responseService = $responseService;
        $this->maintenanceService = $maintenanceService;
    }

    /**
     * @param string|null $view
     * @param string|null $requestUri
     * @param array       $options
     *
     * @return Response
     *
     * @throws TwigError\LoaderError
     * @throws TwigError\RuntimeError
     * @throws TwigError\SyntaxError
     */
    public function getResponse(?string $view = null, ?string $requestUri = null, array $options = []): Response
    {
        $maintenance = $this->maintenanceService->getMaintenance();
        $url = $maintenance->getRedirectPath();

        if (null !== $url && $url !== $requestUri) {
            return $this->redirect($url);
        }

        return $this->responseService->getResponse($view, $options);
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
        return $this->responseService->redirect($url, $status);
    }
}
