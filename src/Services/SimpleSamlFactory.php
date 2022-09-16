<?php

namespace SimpleSAML\Module\oauth2\Services;

use SimpleSAML\Auth\Simple;

class SimpleSamlFactory
{
    function createSimple($authSourceName)
    {
        return new Simple($authSourceName);
    }
}