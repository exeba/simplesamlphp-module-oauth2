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
use SimpleSAML\Module\oauth2\Repositories\ClientRepository;
use SimpleSAML\Session;
use SimpleSAML\Utils\Auth;
use SimpleSAML\Utils\HTTP;
use SimpleSAML\XHTML\Template;

$config = Configuration::getInstance();
$session = Session::getSessionFromRequest();
$oauthconfig = Configuration::getOptionalConfig('module_oauth2.php');

Auth::requireAdmin();

$clientRepository = new ClientRepository();

if (isset($_REQUEST['delete'])) {
    $clientRepository->delete($_REQUEST['delete']);

    HTTP::redirectTrustedURL('registry.php');
}

if (isset($_REQUEST['restore'])) {
    $clientRepository->restoreSecret($_REQUEST['restore']);

    HTTP::redirectTrustedURL('registry.php');
}

$clients = $clientRepository->findAll();

$template = new Template($config, 'oauth2:registry_list');
$template->data['clients'] = $clients;
$template->send();
