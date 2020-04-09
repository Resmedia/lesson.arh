<?php

namespace Service\SocialNetwork\Adapter;

class Google extends Adapter
{
    public function __construct($config)
    {
        parent::__construct($config);

        $this->socialFields = [
            'socialId' => 'id',
            'email' => 'email',
            'name' => 'name',
            'socialPage' => 'link',
            'avatar' => 'picture',
            'sex' => 'gender'
        ];

        $this->provider = 'google';
    }

    /**
     * Get user birthday or null if it is not set
     *
     * @return string|null
     */
    public function getBirthday()
    {
        if (isset($this->_userInfo['birthday'])) {
            $this->userInfo['birthday'] = str_replace('0000', date('Y'), $this->userInfo['birthday']);
            $result = date('d.m.Y', strtotime($this->userInfo['birthday']));
        } else {
            $result = null;
        }
        return $result;
    }

    /**
     * Authenticate and return bool result of authentication
     *
     * @return bool
     */
    public function authenticate()
    {
        $result = false;

        if (isset($_GET['code'])) {
            $params = [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
                'grant_type' => 'authorization_code',
                'code' => $_GET['code']
            ];

            $tokenInfo = $this->post('https://accounts.google.com/o/oauth2/token', $params);

            if (isset($tokenInfo['access_token'])) {
                $params['access_token'] = $tokenInfo['access_token'];

                $userInfo = $this->get('https://www.googleapis.com/oauth2/v1/userinfo', $params);
                if (isset($userInfo[$this->socialFields['socialId']])) {
                    $this->userInfo = $userInfo;
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Prepare params for authentication url
     *
     * @return array
     */
    public function prepareAuthParams()
    {
        return [
            'auth_url' => 'https://accounts.google.com/o/oauth2/auth',
            'auth_params' => [
                'redirect_uri' => $this->redirectUri,
                'response_type' => 'code',
                'client_id' => $this->clientId,
                'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
            ]
        ];
    }
}