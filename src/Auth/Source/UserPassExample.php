<?php

namespace SimpleSAML\Module\oauth2\Auth\Source;

use Exception;
use SimpleSAML\Error;
use SimpleSAML\Module\core\Auth\UserPassBase;
use SimpleSAML\Utils;

/**
 * Example authentication source - username & password.
 *
 * This class is copied from SimpleSAMLphp exampleauth module and
 * adapted to implement Attributes interface
 */
class UserPassExample extends UserPassBase implements Attributes
{
    /**
     * Our users, stored in an associative array. The key of the array is "<username>:<password>",
     * while the value of each element is a new array with the attributes for each user.
     *
     * @var array
     */
    private $users;

    /**
     * Constructor for this authentication source.
     *
     * @param array $info   information about this authentication source
     * @param array $config configuration
     */
    public function __construct(array $info, array $config)
    {
        // Call the parent constructor first, as required by the interface
        parent::__construct($info, $config);

        $this->users = [];

        // Validate and parse our configuration
        foreach ($config as $userpass => $attributes) {
            if (!is_string($userpass)) {
                throw new Exception('Invalid <username>:<password> for authentication source '.$this->authId.': '.$userpass);
            }

            $userpass = explode(':', $userpass, 2);
            if (2 !== count($userpass)) {
                throw new Exception('Invalid <username>:<password> for authentication source '.$this->authId.': '.$userpass[0]);
            }
            $username = $userpass[0];
            $password = $userpass[1];

            try {
                $attributes = Utils\Attributes::normalizeAttributesArray($attributes);
            } catch (Exception $e) {
                throw new Exception('Invalid attributes for user '.$username.' in authentication source '.$this->authId.': '.$e->getMessage());
            }
            $this->users[$username.':'.$password] = $attributes;
        }
    }

    /**
     * Attempt to log in using the given username and password.
     *
     * On a successful login, this function should return the users attributes. On failure,
     * it should throw an exception. If the error was caused by the user entering the wrong
     * username or password, a \SimpleSAML\Error\Error('WRONGUSERPASS') should be thrown.
     *
     * Note that both the username and the password are UTF-8 encoded.
     *
     * @param string $username the username the user wrote
     * @param string $password the password the user wrote
     *
     * @return array associative array with the users attributes
     */
    protected function login($username, $password): array
    {
        $userpass = $username.':'.$password;
        if (!array_key_exists($userpass, $this->users)) {
            throw new Error\Error('WRONGUSERPASS');
        }

        return $this->users[$userpass];
    }

    public function getAttributes(string $username)
    {
        $userEmptyPass = $username.':';
        foreach ($this->users as $userpass => $attributes) {
            if (0 === substr_compare($userpass, $userEmptyPass, 0, strlen($userEmptyPass))) {
                return $attributes;
            }
        }

        throw new Error\Error('UNKWNOWNUSER');
    }
}
