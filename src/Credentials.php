<?php

/**
 * This file is part of the ProxmoxVE PHP API wrapper library (unofficial).
 *
 * @copyright 2014 César Muñoz <zzantares@gmail.com>
 * @license http://opensource.org/licenses/MIT The MIT License.
 */

namespace ProxmoxVE;

use ProxmoxVE\Exception\MalformedCredentialsException;

/**
 * Credentials class. Handles all related data used to connect to a Proxmox
 * server.
 *
 * @author César Muñoz <zzantares@gmail.com>
 */
class Credentials {
    /**
     * @var string The Proxmox hostname (or IP address) to connect to.
     */
    private $hostname;

    /**
     * @var string The credentials username used to authenticate with Proxmox.
     */
    private $username;

    /**
     * @var string The credentials password used to authenticate with Proxmox.
     */
    private $password;

    /**
     * @var string The authentication realm (defaults to "pam" if not provided).
     */
    private $realm;

    /**
     * @var string The Proxmox port (defaults to "8006" if not provided).
     */
    private $port;

    /**
     * @var string The Proxmox system being used (defaults to "pve" if not provided).
     */
    private $system;

    /**
     * the ID of the Token, in the <USER>@<REALM>!<NAME> Format
     *
     * @var string 
     */
    private $tokenId;

    /**
     * the secret of the token
     *
     * @var string
     */
    private $tokenSecret;

    /**
     * boolean if the connector should use the api keys to communicate with proxmox
     *
     * @var boolean
     */
    private $isUsingApi = false;

    /**
     * Construct.
     *
     * @param array|object $credentials This needs to have 'hostname',
     *                                  'username' and 'password' defined.
     */
    public function __construct($credentials) {
        // Get credentials object in valid array form
        $credentials = $this->parseCustomCredentials($credentials);

        if(!$credentials) {
            throw new MalformedCredentialsException('PVE API needs a credentials array.');
        }

        if(strpos($credentials['hostname'], ':') !== false) {
            [$host, $port]  = explode(':', $credentials['hostname']);
            $this->hostname = $host;
            $this->port     = $port;
        } else {
            $this->hostname = $credentials['hostname'];
            $this->port     = $credentials['port'];
        }

        $this->username    = $credentials['username'];
        $this->password    = $credentials['password'];
        $this->realm       = $credentials['realm'];
        $this->system      = $credentials['system'];
        $this->tokenId     = $credentials['token-id'];
        $this->tokenSecret = $credentials['token-secret'];

        // we prioritize API keys over default authentication
        if($this->tokenId && $this->tokenSecret) {
            $this->isUsingApi = true;
        }
    }


    /**
     * Gives back the string representation of this credentials object.
     *
     * @return string Credentials data in a single string.
     */
    public function __toString() {
        return sprintf(
            '[Host: %s:%s], [Username: %s@%s].',
            $this->hostname,
            $this->port,
            $this->username,
            $this->realm
        );
    }


    /**
     * Returns the base URL used to interact with the ProxmoxVE API.
     *
     * @return string The proxmox API URL.
     */
    public function getApiUrl() {
        return 'https://'.$this->hostname.':'.$this->port.'/api2';
    }


    /**
     * Gets the hostname configured in this credentials object.
     *
     * @return string The hostname in the credentials.
     */
    public function getHostname() {
        return $this->hostname;
    }


    /**
     * Gets the username given to this credentials object.
     *
     * @return string The username in the credentials.
     */
    public function getUsername() {
        return $this->username;
    }


    /**
     * Gets the password set in this credentials object.
     *
     * @return string The password in the credentials.
     */
    public function getPassword() {
        return $this->password;
    }


    /**
     * Gets the realm used in this credentials object.
     *
     * @return string The realm in this credentials.
     */
    public function getRealm() {
        return $this->realm;
    }


    /**
     * Gets the port configured in this credentials object.
     *
     * @return string The port in the credentials.
     */
    public function getPort() {
        return $this->port;
    }


    /**
     * Gets the system configured in this credentials object.
     *
     * @return string The port in the credentials.
     */
    public function getSystem() {
        return $this->system;
    }

    /**
     * return wether api keys are used for authentication or not.
     *
     * @return boolean
     */
    public function isUsingApiKeys() {
        return $this->isUsingApi;
    }

    public function getToken() {
        $name = match ($this->system) {
            'pbs' => "PBSAPIToken",
            default => "PVEAPIToken"
        };

        return $name."=".$this->tokenId."=".$this->tokenSecret;
    }


    /**
     * Given the custom credentials object it will try to find the required
     * values to use it as the proxmox credentials, this can be an object with
     * accesible properties, getter methods or an object that uses '__get' to
     * access properties dinamically.
     *
     * @param mixed $credentials
     *
     * @return array|null If credentials are found they are returned as an
     *                    associative array, returns null if object can not be
     *                    used as a credentials provider.
     */
    public function parseCustomCredentials($credentials) {
        if(is_array($credentials)) {
            $requiredKeys    = ['hostname', 'username', 'password'];
            $requiredApiKeys = ['hostname', 'token-id', 'token-secret'];
            $credentialsKeys = array_keys($credentials);


            $found    = count(array_intersect($requiredKeys, $credentialsKeys));
            $foundApi = count(array_intersect($requiredApiKeys, $credentialsKeys));

            if($found != count($requiredKeys) && $foundApi != count($requiredApiKeys)) {
                return null;
            }

            // set defaults

            return [
                'hostname'     => $credentials['hostname'] ?? '',
                'username'     => $credentials['username'] ?? '',
                'password'     => $credentials['password'] ?? '',
                'realm'        => $credentials['realm'] ?? 'pam',
                'port'         => $credentials['port'] ?? '8006',
                'system'       => $credentials['system'] ?? 'pve',
                'token-id'     => $credentials['token-id'] ?? '',
                'token-secret' => $credentials['token-secret'] ?? '',
            ];
        }
    }
}