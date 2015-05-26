<?php namespace Spinegar\Sugar7Wrapper\Clients;

interface ClientInterface {

    /**
     * Gets a new OAuth token.
     * @return string|null On success the token will be returned; null otherwise.
     */
    public function getNewAuthToken();

    /**
     * Authenticate and set the oAuth 2.0 token
     * @return bool True on login success; false otherwise.
     */
    public function connect();

    /**
     * Checks if token is set.  This does not check if the token is valid.
     * @return bool True if set; false otherwise.
     */
    public function check();

    /**
     * Set a username to be authenticated with.
     * @param string $value
     */
    public function setUsername($value);

    /**
     * Set a password to be authenticated with.
     * @param $value
     */
    public function setPassword($value);

    /**
     * Sets the client id for this connection
     * @param string $value
     */
    public function setClientId($value);

    /**
     * Sets the client secret key for this connection
     * @param string $value
     */
    public function setClientSecret($value);

    /**
     * Sets the base URL of client requests.
     * @param string $url
     * @return bool
     */
    public function setUrl($url);


    public function setPlatform($platform);

    public function setClientOption($key, $value);

    public function get($endpoint, $paramters = array());

    public function post($endpoint, $paramters = array());

    public function put($endpoint, $paramters = array());

    public function delete($endpoint, $paramters = array());
}