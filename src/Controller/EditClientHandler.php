<?php

namespace SimpleSAML\Module\oauth2\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Error\NotFound;
use SimpleSAML\Module\oauth2\Form\ClientForm;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Module\oauth2\Services\TemplatedResponseBuilder;
use SimpleSAML\Utils\HTTP;

class EditClientHandler implements RequestHandlerInterface
{
    private $http;
    private $clientRepository;
    private $templatedResponseBuilder;

    public function __construct(
        Http $http,
        ClientRepository $clientRepository,
        TemplatedResponseBuilder $templatedResponseBuilder
    ) {
        $this->http = $http;
        $this->clientRepository = $clientRepository;
        $this->templatedResponseBuilder = $templatedResponseBuilder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $form = new ClientForm('client', $this->getClientOrThrow($request));
        if ($form->isSubmitted() && $form->isSuccess()) {
            $client = $form->getClientEntity();
            $this->clientRepository->persistNewClient($client);

            $this->http->redirectTrustedURL('');
        }

        return $this->templatedResponseBuilder->buildResponse('oauth2:registry/new.twig', [
            'form' => $form,
        ]);
    }

    private function getClientOrThrow(ServerRequestInterface $request)
    {
        $client = $this->clientRepository->getClientEntity($request->getQueryParams()['id']);
        if (is_null($client)) {
            throw new NotFound('Client not found');
        }

        return $client;
    }
}
