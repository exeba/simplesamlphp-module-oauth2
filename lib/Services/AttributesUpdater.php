<?php


namespace SimpleSAML\Module\oauth2\Services;

use SimpleSAML\Module\oauth2\Auth\Source\Attributes;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Module\oauth2\Entity\UserEntity;
use SimpleSAML\Module\oauth2\Repositories\UserRepository;

/**
 * This class updates the user attributes if the corresponding authentication source provides them
 */
class AttributesUpdater
{
    private $userRepository;
    private $authenticationSourceResolver;
    private $attributesProcessor;

    public function __construct(
        UserRepository $userRepository,
        AuthenticationSourceResolver $authenticationSourceResolver,
        AttributesProcessor $attributesProcessor
    )
    {
        $this->userRepository = $userRepository;
        $this->authenticationSourceResolver = $authenticationSourceResolver;
        $this->attributesProcessor = $attributesProcessor;
    }

    public function updateAttributes(UserEntity $user, ClientEntity $client)
    {
        $authSource = $this->authenticationSourceResolver->getAuthSourceFromClient($client);
        if ($authSource instanceof Attributes) {
            $this->updateUserAttributes($user, $authSource);
        }
    }

    private function updateUserAttributes(UserEntity $user, Attributes $authSource)
    {
        $newAttributes = $authSource->getAttributes($user->getIdentifier());
        $newAttributes = $this->attributesProcessor->processAttributes($newAttributes);
        $user->setAttributes($newAttributes);
        $this->userRepository->insertOrUpdate($user);
    }
}
