<?php


namespace SimpleSAML\Module\oauth2\Auth\Source;

interface Attributes
{
    public function getAttributes(string $username);
}
