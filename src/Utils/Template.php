<?php

namespace SimpleSAML\Module\oauth2\Utils;

use SimpleSAML\Configuration;

class Template extends \SimpleSAML\XHTML\Template
{
    public function __construct(Configuration $configuration, $template)
    {
        parent::__construct($configuration, $template);
    }

    public function getContent()
    {
        $this->content = $this->getContents();

        return parent::getContent();
    }
}
