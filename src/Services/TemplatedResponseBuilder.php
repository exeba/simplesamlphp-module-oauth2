<?php


namespace SimpleSAML\Module\oauth2\Services;

use Psr\Http\Message\ResponseInterface;
use SimpleSAML\Configuration;
use SimpleSAML\Module\oauth2\Utils\Template;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class TemplatedResponseBuilder
{
    private $psrHttpFactory;
    private $configuration;

    public function __construct(
        PsrHttpFactory $psrHttpFactory,
        Configuration $configuration)
    {
        $this->psrHttpFactory = $psrHttpFactory;
        $this->configuration = $configuration;
    }

    public function buildResponse(string $template, $data): ResponseInterface
    {
        $config = Configuration::getInstance();
        $template = new Template($config, $template);
        $template->data = $data;

        return $this->psrHttpFactory->createResponse($template);
    }
}
