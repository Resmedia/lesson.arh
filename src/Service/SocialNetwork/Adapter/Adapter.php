<?php

namespace Service\SocialNetwork\Adapter;

abstract class Adapter implements AdapterInterface
{
    /**
     * Social Client ID
     * @var string null
     */
    protected $clientId = null;

    /**
     * Social Client Secret
     * @var string null
     */
    protected $clientSecret = null;

    /**
     * Social Redirect Uri
     * @var string null
     */
    protected $redirectUri = null;

    /**
     * Name of auth provider
     * @var string null
     */
    protected $provider = null;

    /**
     * Social Fields Map for universal keys
     * @var array
     */
    protected $socialFields = [];

    /**
     * Storage for user info
     * @var array
     */
    protected $userInfo = null;

    /**
     * Constructor
     *
     * @param array $config
     * @throws \Exception
     */
    public function __construct($config)
    {
        if (!is_array($config))
            throw new \Exception(
                __METHOD__ . ' expects an array with keys: `client_id`, `client_secret`, `redirect_uri`'
            );

        foreach (['client_id', 'client_secret', 'redirect_uri'] as $param) {
            if (!array_key_exists($param, $config)) {
                throw new \Exception(
                    __METHOD__ . ' expects an array with key: `' . $param . '`'
                );
            } else {
                $property = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $param))));
                $this->$property = $config[$param];
            }
        }
    }

    /**
     * Get user social id or null if it is not set
     * @return string|null
     */
    public function getSocialId()
    {
        $result = null;

        if (isset($this->userInfo[$this->socialFields['socialId']])) {
            $result = $this->userInfo[$this->socialFields['socialId']];
        }

        return $result;
    }

    /**
     * Get user email or null if it is not set
     * @return string|null
     */
    public function getEmail()
    {
        $result = null;

        if (isset($this->userInfo[$this->socialFields['email']])) {
            $result = $this->userInfo[$this->socialFields['email']];
        }

        return $result;
    }

    /**
     * Get user name or null if it is not set
     * @return string|null
     */
    public function getName()
    {
        $result = null;

        if (isset($this->userInfo[$this->socialFields['name']])) {
            $result = $this->userInfo[$this->socialFields['name']];
        }

        return $result;
    }

    /**
     * Get user social page url or null if it is not set
     * @return string|null
     */
    public function getSocialPage()
    {
        $result = null;

        if (isset($this->userInfo[$this->socialFields['socialPage']])) {
            $result = $this->userInfo[$this->socialFields['socialPage']];
        }

        return $result;
    }

    /**
     * Get url of user's avatar or null if it is not set
     * @return string|null
     */
    public function getAvatar()
    {
        $result = null;

        if (isset($this->userInfo[$this->socialFields['avatar']])) {
            $result = $this->userInfo[$this->socialFields['avatar']];
        }

        return $result;
    }

    /**
     * Get user sex or null if it is not set
     * @return string|null
     */
    public function getSex()
    {
        $result = null;

        if (isset($this->userInfo[$this->socialFields['sex']])) {
            $result = $this->userInfo[$this->socialFields['sex']];
        }

        return $result;
    }

    /**
     * Get user birthday in format dd.mm.YYYY or null if it is not sex
     * @return string|null
     */
    public function getBirthday()
    {
        $result = null;

        if (isset($this->userInfo[$this->socialFields['birthday']])) {
            $result = date('d.m.Y', strtotime($this->userInfo[$this->socialFields['birthday']]));
        }

        return $result;
    }

    /**
     * Get authentication url
     *
     * @return string
     */
    public function getAuthUrl()
    {
        $config = $this->prepareAuthParams();

        return $result = $config['auth_url'] . '?' . urldecode(http_build_query($config['auth_params']));
    }

    /**
     * Return name of auth provider
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Make post request and return result
     *
     * @param string $url
     * @param array $params
     * @param bool $parse
     * @return array|string
     */
    protected function post($url, $params, $parse = true)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);

        if ($parse) {
            $result = json_decode($result, true);
        }

        return $result;
    }

    /**
     * Make get request and return result
     *
     * @param $url
     * @param $params
     * @param bool $parse
     * @return mixed
     */
    protected function get($url, $params, $parse = true)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url . '?' . urldecode(http_build_query($params)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);

        if ($parse) {
            $result = json_decode($result, true);
        }

        return $result;
    }
}