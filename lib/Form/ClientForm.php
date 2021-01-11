<?php
/*
 * This file is part of the simplesamlphp.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleSAML\Module\oauth2\Form;

use Nette\Forms\Form;
use SimpleSAML\Auth\Source;
use SimpleSAML\Module;

class ClientForm extends Form
{

    private $clientEntity;

    /**
     * {@inheritdoc}
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->onValidate[] = [$this, 'validateRedirectUri'];

        $this->setMethod('POST');
        $this->addProtection('Security token has expired, please submit the form again');

        $this->addText('name', 'Name of client:')
            ->setMaxLength(255)
            ->setRequired('Set a name')
        ;
        $this->addTextArea('description', 'Description of client:', null, 5);
        $this->addTextArea('redirect_uri', 'Static/enforcing callback-url (one per line)', null, 5)
            ->setRequired('Write one redirect URI at least')
        ;
        $this->addSelect('auth_source', 'Authorization source:')
            ->setItems(Source::getSources(), false)
            ->setPrompt('Pick an AuthSource or blank for default')
            ->setRequired(false)
        ;

        $this->addSubmit('submit', 'Submit');
        $this->addButton('return', 'Return')
            ->setAttribute('onClick', 'parent.location = \''.Module::getModuleURL('oauth2/registry/').'\'')
        ;
    }

    public function validateRedirectUri($form)
    {
        $values = $this->getValues();
        $redirect_uris = $values['redirect_uri'];
        foreach ($redirect_uris as $redirect_uri) {
            if (false === filter_var($redirect_uri, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
                $this->addError('Invalid URI: '.$redirect_uri);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getClientEntity()
    {
        $values = parent::getValues(true);

        $entity = new Module\oauth2\Entity\ClientEntity();

        if ($this->clientEntity) {
            $entity->setIdentifier($this->clientEntity->getIdentifier());
            $entity->setSecret($this->clientEntity->getSecret());
        }
        $entity->setName($values['name']);
        $entity->setDescription($values['description']);
        $entity->setAuthSource($values['auth_source']);
        $entity->setRedirectUri($this->splitRedirectUris($values['redirect_uri']));

        return $entity;
    }

    private function splitRedirectUris($uris)
    {
        $redirect_uris = preg_split("/[\t\r\n]+/", $uris);
        return array_filter($redirect_uris, function ($redirect_uri) {
            return !empty(trim($redirect_uri));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setClientEntity(Module\oauth2\Entity\ClientEntity $entity)
    {
        $this->clientEntity = $entity;
        return $this->setDefaults([
            'name' => $entity->getName(),
            'description' => $entity->getDescription(),
            'auth_source' => $entity->getAuthSource(),
            'redirect_uri' => implode("\n", $entity->getRedirectUri())
        ]);
    }
}
