<?php


namespace SimpleSAML\Module\oauth2\Controller;


use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Configuration;
use SimpleSAML\Error\BadRequest;
use SimpleSAML\Module;
use SimpleSAML\Module\oauth2\AuthRequestSerializer;
use SimpleSAML\Module\oauth2\Entity\UserEntity;
use SimpleSAML\Module\oauth2\Form\AuthorizeForm;
use SimpleSAML\XHTML\Template;

class AuthorizeChoiceHandler implements RequestHandlerInterface
{
    private $responseFactory;
    private $authorizationServer;
    private $authRequestSerializer;

    public function __construct(
            ResponseFactoryInterface $responseFactory,
            AuthorizationServer $authorizationServer,
            AuthRequestSerializer $authRequestSerializer)
    {
        $this->responseFactory = $responseFactory;
        $this->authorizationServer = $authorizationServer;
        $this->authRequestSerializer = $authRequestSerializer;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $form = new AuthorizeForm('authorize');
        if (!$form->isSubmitted() || !$form->isValid()) {
            throw new BadRequest('AuthorizationRequest not found');
        }
        $form->fireEvents();

        $authorizationRequest = $this->authRequestSerializer->deserialize($form->getValues()['authRequest']);
        $authorizationRequest->setAuthorizationApproved($form->hasPressed('allow'));

        return $this->authorizationServer->completeAuthorizationRequest($authorizationRequest, $this->newResponse());
    }

    private function newResponse() {
        return $this->responseFactory->createResponse();
    }
}
