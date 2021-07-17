<?php


namespace SimpleSAML\Module\oauth2\Services;

use SimpleSAML\Auth\Simple;
use SimpleSAML\Auth\Source;
use SimpleSAML\Module\oauth2\Auth\Source\Attributes;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Module\oauth2\Entity\UserEntity;
use SimpleSAML\Module\oauth2\Repositories\UserRepository;

class AttributesUpdater
{
    private $userRepository;
    private $defaultAuthenticationSource;
    private $singleValuedAttributes;

    public function __construct(
        UserRepository $userRepository,
        $defaultAuthenticationSource,
        $singleValuedAttributes
    )
    {
        $this->userRepository = $userRepository;
        $this->defaultAuthenticationSource = $defaultAuthenticationSource;
        $this->singleValuedAttributes = $singleValuedAttributes ?? [];
    }

    public function updateAttributes(UserEntity $user, ClientEntity $client)
    {
        $authSource = $this->getAuthenticationSource($client);
        if ($authSource instanceof Attributes) {
            $newAttributes = $this->processAttributes($authSource->getAttributes($user->getIdentifier()));
            $user->setAttributes($newAttributes);
            $this->userRepository->insertOrCreate($user);
        }
    }

    private function getAuthenticationSource(ClientEntity $client): Source
    {
        return (new Simple($this->getAuthSourceName($client)))->getAuthSource();
    }

    private function getAuthSourceName(ClientEntity $client)
    {
        return $client->getAuthSource() ?? $this->defaultAuthenticationSource;
    }

    private function processAttributes($attributes)
    {
        $processedAttributes = [];
        foreach ($attributes as $name => $value) {
            $processedAttributes[$name] = $this->isSingleValued($name) ? $value[0] : $value;
        }

        return $processedAttributes;
    }

    public function isSingleValued($attribute)
    {
        return in_array($attribute, $this->singleValuedAttributes, true);
    }
}
