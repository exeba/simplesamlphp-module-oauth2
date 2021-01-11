<?php


namespace SimpleSAML\Module\oauth2\Controller;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleSAML\Error\NotFound;
use SimpleSAML\Module\oauth2\Entity\ClientEntity;
use SimpleSAML\Module\oauth2\Form\ClientForm;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Module\oauth2\Services\TemplatedResponseBuilder;
use SimpleSAML\Utils\HTTP;
use SimpleSAML\Utils\Random;

class EditClientHandler implements RequestHandlerInterface
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
        $client = $this->getClientOrThrow($request);
        $form = new ClientForm('client');
        $form->setClientEntity($client);
        if ($form->isSubmitted() && $form->isSuccess()) {
            $client = $form->getClientEntity();
            $this->clientRepository->updateClient($client);

            HTTP::redirectTrustedURL('index.php');
        }

        return $this->templatedResponseBuilder->buildResponse('oauth2:registry/new.twig', [
            'form' => $form,
        ]);
    }

    private function getClientOrThrow(ServerRequestInterface $request)
    {
        $client = $this->clientRepository->getClientEntity($request->getQueryParams()['id']);
        if (is_null($client)) {
            throw new NotFound("Client not found");
        }

        return $client;
    }
}
