<?php


namespace SimpleSAML\Module\oauth2\Controller;


use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Configuration;
use SimpleSAML\Module;
use SimpleSAML\Module\oauth2\AuthRequestSerializer;
use SimpleSAML\Module\oauth2\Form\AuthorizeForm;
use SimpleSAML\Module\oauth2\Services\AuthenticationService;
use SimpleSAML\Module\oauth2\Services\TemplatedResponseBuilder;
use SimpleSAML\Module\oauth2\Utils\Template;

class AuthorizeRequestHandler implements RequestHandlerInterface
{

    private $userRepository;
    private $authorizationServer;
    private $authRequestSerializer;
    private $authenticationService;
    private $templatedResponseBuilder;

    public function __construct(
            UserRepositoryInterface $userRepository,
            AuthorizationServer $authorizationServer,
            AuthRequestSerializer $authRequestSerializer,
            AuthenticationService $authenticationService,
            TemplatedResponseBuilder $templatedResponseBuilder)
    {
        $this->userRepository = $userRepository;
        $this->authorizationServer = $authorizationServer;
        $this->authenticationService = $authenticationService;
        $this->authRequestSerializer = $authRequestSerializer;
        $this->templatedResponseBuilder = $templatedResponseBuilder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userEntity = $this->authenticationService->getUserEntity($request);
        $authRequest = $this->authorizationServer->validateAuthorizationRequest($request);

        $this->userRepository->insertOrCreate($userEntity);
        $authRequest->setUser($userEntity);

        $serializedRequest = $this->authRequestSerializer->serialize($authRequest);
        $form = new AuthorizeForm('authorize');
        $form->setDefaults(['authRequest' => $serializedRequest]);
        $form->setAction(Module::getModuleURL('oauth2/authorize_choice.php'));

        return $this->templatedResponseBuilder->buildResponse('oauth2:authorize', [
            'authRequest' => $authRequest,
            'form' => $form,
        ]);
    }
}
