<?php namespace Spinegar\Sugar7Wrapper\Clients;

use Guzzle\Common\Event;
use Guzzle\Http\Client;
use Guzzle\Http\Query;

/**
 * SugarCRM 6 Rest Client
 *
 * @package   SugarCRM 7 Rest Wrapper
 * @category  Libraries
 * @author  Asa Freedman
 * @license MIT License
 */
class Sugar6 implements ClientInterface
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
     * Endpoint for REST requests :)
     * @var string
     */
    private $rest_endpoint = 'service/v4_1/rest.php';

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
        // format post data
        $parameters = array(
            "method" => 'login',
            "input_type" => 'JSON',
            "response_type" => 'JSON',
            "rest_data" => json_encode([
                'user_auth' => [
                    'user_name' => $this->username,
                    'password' => md5($this->password)
                ]
            ])
        );

        $request = $this->client->post($this->url, null, $parameters);
        $result = $request->send()->json();

        if (isset($result['id'])) {
            $this->setToken($result['id']);
            return $result['id'];
        } else {
            return false;
        }
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

    public function setToken($value)
    {
        $this->token = $value;
    }

    public function get($endpoint, $parameters = array())
    {
        if (!self::check()) {
            self::connect();
        }

        $request = $this->client->get($this->url . $endpoint);

        $query = $request->getQuery();

        foreach ($parameters as $key => $value) {
            $query->add($key, $value);
        }

        $response = $request->send()->json();

        if (!$response)
            return false;

        return $response;
    }

    public function post($endpoint, $parameters = array())
    {
        if (!self::check())
            self::connect();

        $request = $this->client->post($this->url . $endpoint, null, json_encode($parameters));
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

        $request = $this->client->delete($this->url . $endpoint, null, json_encode($parameters));
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


    public function setClientId($value) {}
    public function setClientSecret($value) {}
    public function setPlatform($value) {}
    public function getPlatform() {}
    public function getFile($endpoint, $destinationFile, $parameters = array()) {}
    public function postFile($endpoint, $parameters = array()) {}
}
