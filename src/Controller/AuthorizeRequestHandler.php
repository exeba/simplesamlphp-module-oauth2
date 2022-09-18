<?php

namespace SimpleSAML\Module\oauth2\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Module;
use SimpleSAML\Module\oauth2\AuthRequestSerializer;
use SimpleSAML\Module\oauth2\Form\AuthorizeForm;
use SimpleSAML\Module\oauth2\Services\TemplatedResponseBuilder;

class AuthorizeRequestHandler implements RequestHandlerInterface
{
    private $authRequestSerializer;
    private $templatedResponseBuilder;

    public function __construct(
        AuthRequestSerializer $authRequestSerializer,
        TemplatedResponseBuilder $templatedResponseBuilder
    ) {
        $this->authRequestSerializer = $authRequestSerializer;
        $this->templatedResponseBuilder = $templatedResponseBuilder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $authnRequest = $request->getAttribute('authnRequest');
        $serializedRequest = $this->authRequestSerializer->serialize($authnRequest);
        $form = new AuthorizeForm('authorize');
        $form->setDefaults(['authRequest' => $serializedRequest]);
        $form->setAction(Module::getModuleURL('oauth2/authorize_choice'));

        return $this->templatedResponseBuilder->buildResponse('oauth2:authorize', [
            'authRequest' => $authnRequest,
            'form' => $form,
        ]);
    }
}
