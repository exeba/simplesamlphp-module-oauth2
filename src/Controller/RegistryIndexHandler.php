<?php

namespace SimpleSAML\Module\oauth2\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Module\oauth2\Services\TemplatedResponseBuilder;
use SimpleSAML\Utils\HTTP;

class RegistryIndexHandler implements RequestHandlerInterface
{
    private $clientRepository;
    private $templatedResponseBuilder;

    public function __construct(
        ClientRepository $clientRepository,
        TemplatedResponseBuilder $templatedResponseBuilder
    ) {
        $this->clientRepository = $clientRepository;
        $this->templatedResponseBuilder = $templatedResponseBuilder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (isset($_REQUEST['delete'])) {
            $this->clientRepository->delete($_REQUEST['delete']);

            HTTP::redirectTrustedURL('registry');
        }

        if (isset($_REQUEST['restore'])) {
            $this->clientRepository->restoreSecret($_REQUEST['restore']);

            HTTP::redirectTrustedURL('registry');
        }

        return $this->templatedResponseBuilder->buildResponse('oauth2:registry/index.twig', [
            'clients' => $this->clientRepository->findAll(),
        ]);
    }
}
