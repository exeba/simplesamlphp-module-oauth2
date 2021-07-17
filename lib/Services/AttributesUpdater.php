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
    private $attributesProcessor;

    public function __construct(
        UserRepository $userRepository,
        $defaultAuthenticationSource,
        AttributesProcessor $attributesProcessor
    )
    {
        $this->userRepository = $userRepository;
        $this->defaultAuthenticationSource = $defaultAuthenticationSource;
        $this->attributesProcessor = $attributesProcessor;
    }

    public function updateAttributes(UserEntity $user, ClientEntity $client)
    {
        $authSource = $this->getAuthenticationSource($client);
        if ($authSource instanceof Attributes) {
            $newAttributes = $authSource->getAttributes($user->getIdentifier());
            $newAttributes = $this->attributesProcessor->processAttributes($newAttributes);
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
}
