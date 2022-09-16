<?php


namespace SimpleSAML\Module\oauth2\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Module\oauth2\Repositories\AccessTokenRepository;
use SimpleSAML\Module\oauth2\Repositories\AuthCodeRepository;
use SimpleSAML\Module\oauth2\Services\TemplatedResponseBuilder;

class ShowUserTokensHandler implements RequestHandlerInterface
{
    private $authCodeRepository;
    private $accessTokenRepository;
    private $templatedResponseBuilder;

    public function __construct(
        AccessTokenRepository $accessTokenRepository,
        AuthCodeRepository $authCodeRepository,
        TemplatedResponseBuilder $templatedResponseBuilder
    ) {
        $this->authCodeRepository = $authCodeRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->templatedResponseBuilder = $templatedResponseBuilder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');

        $accessTokens = $this->accessTokenRepository->getTokensForUser($user->getIdentifier());
        $authCodes = $this->authCodeRepository->getActiveTokensForUser($user->getIdentifier());

        return $this->templatedResponseBuilder->buildResponse('oauth2:owner/show_grants.twig', [
            'authCodes' => $authCodes,
            'accessTokens' => $accessTokens,
            'authSource' => $request->getAttribute('authSource'),
        ]);
    }
}
