<?php


namespace SimpleSAML\Module\oauth2\Controller;


use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
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
use SimpleSAML\Module\oauth2\Utils\Template;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class AuthorizeRequestHandler implements RequestHandlerInterface
{

    private $userRepository;
    private $authorizationServer;
    private $authRequestSerializer;
    private $authenticationService;
    private $oauth2config;

    public function __construct(
            UserRepositoryInterface $userRepository,
            AuthorizationServer $authorizationServer,
            AuthRequestSerializer $authRequestSerializer,
            AuthenticationService $authenticationService,
            Configuration $oauth2config)
    {
        $this->userRepository = $userRepository;
        $this->authorizationServer = $authorizationServer;
        $this->authenticationService = $authenticationService;
        $this->authRequestSerializer = $authRequestSerializer;
        $this->oauth2config = $oauth2config;
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

        $config = Configuration::getInstance();
        $template = new Template($config, 'oauth2:authorize');
        $template->data['authRequest'] = $authRequest;
        $template->data['form'] = $form;

        $psrHttpFactory = new PsrHttpFactory(
            new ServerRequestFactory(),
            new StreamFactory(),
            new UploadedFileFactory(),
            new ResponseFactory());

        return $psrHttpFactory->createResponse($template);
    }

}
