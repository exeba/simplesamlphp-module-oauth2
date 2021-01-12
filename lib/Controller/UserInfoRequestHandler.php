<?php


namespace SimpleSAML\Module\oauth2\Controller;


use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Module\oauth2\Repositories\AccessTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\UserRepository;

class UserInfoRequestHandler implements RequestHandlerInterface
{
    const USER_ID_ATTRIBUTE_NAME = 'oauth_user_id';

    private $userRepository;

    /* TODO: use interfaces */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse($this->getUserAttributes($request));
    }

    private function getUserAttributes(ServerRequestInterface $request): array {
        $userId = $request->getAttributes()[self::USER_ID_ATTRIBUTE_NAME];
        $attributes = $this->userRepository->getUser($userId)->getAttributes();

        // TODO: store already sanitized attributes
        $attributes = array_map(function($attributeArray) {
            return $attributeArray[0];
        }, $attributes);

        return $attributes;
    }
}
