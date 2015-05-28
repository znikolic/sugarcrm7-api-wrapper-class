<?php namespace Spinegar\Sugar7Wrapper\Clients;

use Guzzle\Common\Event;
use Guzzle\Http\Client;
use Guzzle\Http\Query;

/**
 * SugarCRM 7 Rest Client
 *
 * @package   SugarCRM 7 Rest Wrapper
 * @category  Libraries
 * @author  Sean Pinegar
 * @license MIT License
 * @link   https://github.com/spinegar/sugarcrm7-api-wrapper-class
 */
class Sugar7 implements ClientInterface
{

    /**
     * Variable: $url
     * Description:  A Sugar Instance.
     */
    private $url;

    /**
     * Variable: $username
     * Description:  A SugarCRM User.
     */
    private $username;

    /**
     * Variable: $password
     * Description:  The password for the $username SugarCRM account
     */
    private $password;

    /**
     * The client id used for sugar when identifying this connection
     * @var string
     */
    private $client_id = 'sugar';

    /**
     * The client secret key for authenticating connections with sugar
     * @var string
     */
    private $client_secret = '';

    /**
     * Variable: $platform
     * Description:  A Sugar Instance.
     */
    private $platform = 'mobile';


    private $rest_endpoint = 'rest/v10/';
    /**
     * Variable: $token
     * Description:  OAuth 2.0 token
     */
    protected $token;

    /**
     * Variable: $client
     * Description:  Guzzle Client
     */
    protected $client;

    function __construct()
    {
        $this->client = new Client();
    }

    public function getNewAuthToken()
    {
        $request = $this->client->post($this->url . 'oauth2/token', null, array(
            'grant_type' => 'password',
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'username' => $this->username,
            'password' => $this->password,
            'platform' => $this->platform,
        ));

        $result = $request->send()->json();
        return $result['access_token'];
    }

    public function connect()
    {
        $token = $this->getNewAuthToken();

        if (!$token) {
            return false;
        }

        self::setToken($token);
        $eventDispatcher = $this->client->getEventDispatcher();
        $eventDispatcher->addListener('request.before_send', array($this, 'beforeSendRequest'));
        $eventDispatcher->addListener('request.error', array($this, 'refreshToken'));

        return true;
    }

    public function check()
    {
        if (!$this->token)
            return false;

        return true;
    }

    public function setClientOption($key, $value)
    {
        $this->client->setDefaultOption($key, $value);
    }

    public function setUrl($url, $restful = true)
    {
        if (!$url) {
            return false;
        }

        $url = rtrim($url, '/') . '/';

        if ($restful) {
            $url .= $this->rest_endpoint;
        }

        $this->url = $url;
        $this->client->setBaseUrl($this->url);

        return true;
    }

    public function getUrl()
    {
        return $this->url;
    }
    public function setUsername($value)
    {
        $this->username = $value;

        return $this;
    }

    public function setPassword($value)
    {
        $this->password = $value;

        return $this;
    }

    public function setClientId($value)
    {
        $this->client_id = $value;

        return $this;
    }

    public function setClientSecret($value)
    {
        $this->client_secret = $value;

        return $this;
    }

    public function setPlatform($value)
    {
        $this->platform = $value;

        return $this;
    }

    public function getPlatform()
    {
        return $this->platform;

        return $this;
    }

    public function setToken($value)
    {
        $this->token = $value;
    }

    public function get($endpoint, $parameters = array())
    {
        if (!self::check()) {
            self::connect();
        }

        $request = $this->client->get($endpoint);

        $query = $request->getQuery();

        foreach ($parameters as $key => $value) {
            $query->add($key, $value);
        }

        $response = $request->send()->json();

        if (!$response)
            return false;

        return $response;
    }

    public function getFile($endpoint, $destinationFile, $parameters = array())
    {
        if (!self::check())
            self::connect();

        $request = $this->client->get($endpoint);

        $query = $request->getQuery();

        foreach ($parameters as $key => $value) {
            $query->add($key, $value);
        }

        $request->setResponseBody($destinationFile);

        $response = $request->send();

        if (!$response)
            return false;

        return $response;
    }

    public function postFile($endpoint, $parameters = array())
    {
        if (!self::check())
            self::connect();

        $request = $this->client->post($endpoint, array(), $parameters);
        $request->setHeader('Content-Type', 'multipart/form-data');
        $result = $request->send();

        if (!$result)
            return false;

        return $result;
    }

    public function post($endpoint, $parameters = array())
    {
        if (!self::check())
            self::connect();

        $request = $this->client->post($endpoint, null, json_encode($parameters));
        $response = $request->send()->json();

        if (!$response)
            return false;

        return $response;
    }

    public function put($endpoint, $parameters = array())
    {
        if (!self::check())
            self::connect();

        $request = $this->client->put($endpoint, null, json_encode($parameters));
        $response = $request->send()->json();

        if (!$response)
            return false;

        return $response;
    }

    public function delete($endpoint, $parameters = array())
    {
        if (!self::check())
            self::connect();

        $request = $this->client->delete($endpoint);
        $response = $request->send()->json();


        if (!$response)
            return false;

        return $response;
    }

    public function refreshToken(Event $event)
    {
        if ($event['response']->getStatusCode() === 401) {
            $this->setToken($this->getNewAuthToken());

            $event['response'] = $event['request']->send();
            $event->stopPropagation();
        }
    }

    public function beforeSendRequest(Event $event)
    {
        $event['request']->setHeader('OAuth-Token', $this->token);
    }
}
