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

use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Form;

class AuthorizeForm extends Form
{
    private $pressedButton;

    /**
     * {@inheritdoc}
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->setMethod('POST');
        // $this->addProtection('Security token has expired, please submit the form again');

        $this->addHidden('authRequest');

        $this->addSubmit('allow', 'Allow')
            ->setHtmlAttribute('class', 'pure-button')
            ->onClick[] = [$this, 'formSubmittedBy'];

        $this->addSubmit('deny', 'Deny')
            ->setHtmlAttribute('class', 'pure-button pure-button-red')
            ->onClick[] = [$this, 'formSubmittedBy'];
    }

    public function formSubmittedBy(SubmitButton $submit)
    {
        $this->pressedButton = $submit;
    }

    public function hasPressed($buttonName)
    {
        return $buttonName === $this->pressedButton->getName();
    }
}
