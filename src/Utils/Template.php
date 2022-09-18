<?php

namespace SimpleSAML\Module\oauth2\Utils;

use SimpleSAML\Configuration;

class Template extends \SimpleSAML\XHTML\Template
{
    public function __construct(Configuration $configuration, $template, $defaultDictionary = null)
    {
        parent::__construct($configuration, $template, $defaultDictionary);
    }

    public function getContent()
    {
        $this->content = $this->getContents();

        return parent::getContent();
    }
}
