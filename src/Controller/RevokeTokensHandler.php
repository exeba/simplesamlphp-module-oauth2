<?php

namespace SimpleSAML\Module\oauth2\Controller;

use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Error\BadRequest;
use SimpleSAML\Module\oauth2\Services\RevokerService;

class RevokeTokensHandler implements RequestHandlerInterface
{
    private $revokerService;

    public function __construct(RevokerService $revokerService)
    {
        $this->revokerService = $revokerService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');
        $revokeRequest = $request->getParsedBody();
        $tokenType = $revokeRequest['tokenType'];
        $tokenIdentifier = $revokeRequest['tokenIdentifier'];

        switch ($tokenType) {
            case 'authCode':
                $this->revokerService->revokeAuthCodeIfOwner($tokenIdentifier, $user->getIdentifier());
                break;
            case 'refresh':
                $this->revokerService->revokeRefreshTokenIfOwner($tokenIdentifier, $user->getIdentifier());
                break;
            case 'access':
                $this->revokerService->revokeAccessTokenIfOwner($tokenIdentifier, $user->getIdentifier());
                break;
            default:
                throw new BadRequest("Unknown token type: {$request['tokenType']}");
        }

        return new RedirectResponse($request->getHeader('Referer')[0]);
    }
}
