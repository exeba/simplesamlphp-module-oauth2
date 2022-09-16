<?php

namespace SimpleSAML\Module\oauth2\Services;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use Psr\Http\Message\RequestInterface;
use SimpleSAML\Auth\Source;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;

class AuthenticationSourceResolver
{
    private $simpleSamlFactory;
    private $defaultAuthenticationSourceId;

    public function __construct(
        SimpleSamlFactory $simpleSamlFactory,
                          $defaultAuthenticationSourceId)
    {
        $this->simpleSamlFactory = $simpleSamlFactory;
        $this->defaultAuthenticationSourceId = $defaultAuthenticationSourceId;
    }

    public function getAuthSourceFromClient(ClientEntityInterface $client): Source
    {
        return $this->simpleSamlFactory->createSimple($this->getAuthSourceIdFromClient($client))->getAuthSource();
    }

    public function getAuthSourceIdFromClient(ClientEntityInterface $client)
    {
        if ($client instanceof ClientEntity) {
            return $client->getAuthSource() ?? $this->defaultAuthenticationSourceId;
        }

        return $this->defaultAuthenticationSourceId;
    }

    public function getAuthSourceFromRequest(RequestInterface $request): Source
    {
        return $this->simpleSamlFactory->createSimple($this->getAuthSourceIdFromRequest($request))->getAuthSource();
    }

    public function getAuthSourceIdFromRequest(RequestInterface $request)
    {
        return $request->getAttributes()['authSource'] ?? $this->defaultAuthenticationSourceId;
    }

    public function getDefaultAuthSource(): Source
    {
        return $this->simpleSamlFactory->createSimple($this->defaultAuthenticationSourceId)->getAuthSource();
    }


}