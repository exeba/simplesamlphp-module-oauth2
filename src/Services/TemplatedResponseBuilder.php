<?php

namespace SimpleSAML\Module\oauth2\Services;

use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class TemplatedResponseBuilder
{
    private $psrHttpFactory;
    private $templateFactory;

    public function __construct(
        PsrHttpFactory $psrHttpFactory,
        TemplateFactory $templateFactory)
    {
        $this->psrHttpFactory = $psrHttpFactory;
        $this->templateFactory = $templateFactory;
    }

    public function buildResponse(string $template, $data): ResponseInterface
    {
        $template = $this->templateFactory->buildTemplate($template);
        $template->data = $data;

        return $this->psrHttpFactory->createResponse($template);
    }
}
