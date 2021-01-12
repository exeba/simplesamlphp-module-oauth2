<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Module\oauth2\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ClientEntity implements ClientEntityInterface
{
    use EntityTrait, ClientTrait;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $authSource;

    /**
     * @var string
     */
    private $description;

    private $scopes = ['base'];

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @param string $redirectUri
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @return string
     */
    public function getAuthSource()
    {
        return $this->authSource;
    }

    /**
     * @param string $authSource
     */
    public function setAuthSource($authSource)
    {
        $this->authSource = $authSource;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setScopes($scopes)
    {
        $this->scopes = $scopes;
    }

    public function getScopes()
    {
        return $this->scopes;
    }

    public function isConfidential()
    {
        return !is_null($this->getSecret());
    }
}
