<?php
/*
 * This file is part of the simplesamlphp-module-oauth2.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use SimpleSAML\Configuration;
use SimpleSAML\Module;
use SimpleSAML\Module\oauth2\Form\ClientForm;
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Utils\Auth;
use SimpleSAML\Utils\HTTP;
use SimpleSAML\XHTML\Template;

Auth::requireAdmin();

/* Load simpleSAMLphp, configuration and metadata */
$client_id = $_REQUEST['id'];
$action = Module::getModuleURL('oauth2/edit.php', ['id' => $client_id]);
$config = Configuration::getInstance();

$clientRepository = new ClientRepository();
$client = $clientRepository->find($client_id);
if (!$client) {
    header('Content-type: text/plain; utf-8', true, 500);

    echo 'Client not found';

    return;
}

$form = new ClientForm('client');
$form->setAction($action);
$form->setDefaults($client);

if ($form->isSubmitted() && $form->isSuccess()) {
    $client = $form->getValues();

    $clientRepository->updateClient(
        $client_id,
        $client['name'],
        $client['description'],
        $client['auth_source'],
        $client['redirect_uri']
    );

    HTTP::redirectTrustedURL('index.php');
}

$template = new Template($config, 'oauth2:registry/edit');
$template->data['form'] = $form;
$template->send();
