<?php

namespace SimpleSAML\Module\oauth2\Services;

use SimpleSAML\Configuration;
use SimpleSAML\Module\oauth2\Utils\Template;

class TemplateFactory
{
    private $configuration;

    public function __construct(
        Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function buildTemplate(string $template): Template
    {
        return new Template($this->configuration, $template);
    }
}
