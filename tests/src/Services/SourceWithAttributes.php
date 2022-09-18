<?php

namespace SimpleSAML\Test\Module\oauth2\Services;

use SimpleSAML\Auth\Source;
use SimpleSAML\Module\oauth2\Auth\Source\Attributes;

abstract class SourceWithAttributes extends Source implements Attributes
{
}
