<?php


namespace SimpleSAML\Module\oauth2\Controller;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Module\oauth2\Repositories\AccessTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\AuthCodeRepository;
use SimpleSAML\Module\oauth2\Repositories\RefreshTokenRepository;
use SimpleSAML\Module\oauth2\Services\AuthenticationService;
use SimpleSAML\Module\oauth2\Services\TemplatedResponseBuilder;

class ShowActiveTokensHandler  implements RequestHandlerInterface
{
    private $authenticantionService;
    private $authCodeRepository;
    private $accessTokenRepository;
    private $refreshTokenRepository;
    private $templatedResponseBuilder;

    public function __construct(
        AuthenticationService $authenticationService,
        AccessTokenRepository $accessTokenRepository,
        RefreshTokenRepository $refreshTokenRepository,
        AuthCodeRepository $authCodeRepository,
        TemplatedResponseBuilder $templatedResponseBuilder)
    {
        $this->authenticantionService = $authenticationService;
        $this->authCodeRepository = $authCodeRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->templatedResponseBuilder = $templatedResponseBuilder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->refreshTokenRepository->removeExpiredTokens();
        $this->accessTokenRepository->removeExpiredTokens();
        $this->authCodeRepository->removeExpiredTokens();

        $user = $this->authenticantionService->getUserEntity($request);
        $accessTokens = $this->accessTokenRepository->getActiveTokensForUser($user->getIdentifier());

        $refreshTokens = [];
        foreach ($accessTokens as $accessToken) {
            $refreshToken = $this->refreshTokenRepository->getRefreshTokenFromAccessToken($accessToken->getIdentifier());
            $refreshTokens[] = $refreshToken;
        }

        return $this->templatedResponseBuilder->buildResponse('oauth2:owner/show_grants.twig', [
            'accessTokens' => $accessTokens,
            'refreshTokens' => $refreshTokens,
        ]);
    }
}
