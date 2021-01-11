<?php


namespace SimpleSAML\Module\oauth2\Controller;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Module\oauth2\Form\ClientForm;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Module\oauth2\Services\TemplatedResponseBuilder;
use SimpleSAML\Utils\HTTP;
use SimpleSAML\Utils\Random;

class NewClientHandler implements RequestHandlerInterface
{

    private $clientRepository;
    private $templatedResponseBuilder;

    public function __construct(
        ClientRepository $clientRepository,
        TemplatedResponseBuilder $templatedResponseBuilder)
    {
        $this->clientRepository = $clientRepository;
        $this->templatedResponseBuilder = $templatedResponseBuilder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $form = new ClientForm('client');
        if ($form->isSubmitted() && $form->isSuccess()) {
            $client = $form->getClientEntity();
            $client->setIdentifier(Random::generateID());
            $client->setSecret(Random::generateID());

            $this->clientRepository->persistNewClient($client);

            HTTP::redirectTrustedURL('index.php');
        }

        return $this->templatedResponseBuilder->buildResponse('oauth2:registry/new.twig', [
            'form' => $form,
        ]);
    }
}
