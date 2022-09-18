<?php

namespace SimpleSAML\Module\oauth2\Entity;

trait RevocableTrait
{
    private $isRevoked = false;

    public function setRevoked($isRevoked)
    {
        $this->isRevoked = $isRevoked;
    }

    public function isRevoked()
    {
        return $this->isRevoked;
    }
}
