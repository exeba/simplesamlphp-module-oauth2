<?php

namespace SimpleSAML\Module\oauth2\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Module\oauth2\Form\ClientForm;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Module\oauth2\Services\TemplatedResponseBuilder;
use SimpleSAML\Utils\HTTP;
use SimpleSAML\Utils\Random;

class NewClientHandler implements RequestHandlerInterface
{
    private $clientRepository;
    private $templatedResponseBuilder;
    private $http;
    private $random;

    public function __construct(
        ClientRepository $clientRepository,
        TemplatedResponseBuilder $templatedResponseBuilder,
        HTTP $http,
        Random $random
    ) {
        $this->clientRepository = $clientRepository;
        $this->templatedResponseBuilder = $templatedResponseBuilder;
        $this->http = $http;
        $this->random = $random;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $form = new ClientForm('client');
        if ($form->isSubmitted() && $form->isSuccess()) {
            $client = $form->getClientEntity();
            $client->setIdentifier($this->random->generateID());
            $client->setSecret($this->random->generateID());

            $this->clientRepository->persistNewClient($client);

            $this->http->redirectTrustedURL('');
        }

        return $this->templatedResponseBuilder->buildResponse('oauth2:registry/new.twig', [
            'form' => $form,
        ]);
    }
}
