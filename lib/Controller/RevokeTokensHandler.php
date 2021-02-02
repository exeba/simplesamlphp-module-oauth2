<?php


namespace SimpleSAML\Module\oauth2\Controller;


use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Error\BadRequest;
use SimpleSAML\Module\oauth2\Repositories\AccessTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\AuthCodeRepository;
use SimpleSAML\Module\oauth2\Repositories\RefreshTokenRepository;
use SimpleSAML\Module\oauth2\Services\AuthenticationService;
use SimpleSAML\Module\oauth2\Services\RevokerService;
use SimpleSAML\Module\oauth2\Services\TemplatedResponseBuilder;

class RevokeTokensHandler  implements RequestHandlerInterface
{
    private $authenticantionService;
    private $revokerService;

    public function __construct(
        AuthenticationService $authenticationService,
        RevokerService $revokerService)
    {
        $this->authenticantionService = $authenticationService;
        $this->revokerService = $revokerService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $this->authenticantionService->getUserEntity($request);
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
