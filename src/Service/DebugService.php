<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class DebugService
{
    /**
     * @var bool
     */
    private $isDebug;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var Request
     */
    private $request;

    /**
     * DebugService constructor.
     *
     * @param FlashBagInterface $flashBag
     * @param RequestStack      $request
     * @param bool              $isDebug
     */
    public function __construct(FlashBagInterface $flashBag, RequestStack $request, bool $isDebug)
    {
        $this->isDebug = $isDebug;
        $this->flashBag = $flashBag;
        $this->request = $request->getMasterRequest();
    }

    /**
     * @param string      $type
     * @param string|null $title
     * @param string|null $content
     *
     * @return $this
     */
    public function displayDebugMessage(
        ?string $type = 'success',
        ?string $title = 'debug.info',
        ?string $content = null
    ): self {
        if ($this->isDebug) {
            $message = [
                'title' => $title,
                'message' => $content,
                'context' => $this->getVarDumpedContext(),
            ];
            $this->flashBag->add($type, $message);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getVarDumpedContext(): string
    {
        ob_start();
        var_dump($this->getContext());

        return ob_get_clean();
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        $context = [];

        $context['masterRequest'] = $this->request;
        $context['backtrace'] = $this->getDebugBacktrace();
        $context['definedVars'] = $this->getDefinedVars();

        return $context;
    }

    /**
     * @return array
     */
    public function getDebugBacktrace(): array
    {
        return debug_backtrace();
    }

    /**
     * @return array
     */
    public function getDefinedVars(): array
    {
        return get_defined_vars();
    }
}
