<?php

declare(strict_types=1);

namespace Controller;

use Exception;
use Framework\BaseController;
use Service\User\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends BaseController
{
    // Need put it main config
    private $adapterConfigs = [
        'Google' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => 'http://localhost/auth?provider=google'
        ],
        'Facebook' => [
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => 'http://localhost/auth?provider=facebook'
        ]
    ];

    public function getAdapters()
    {
        $adapters = [];
        foreach ($this->adapterConfigs as $adapter => $settings) {
            $class = 'Service\SocialNetwork\Adapter\\' . $adapter;
            $adapters[$adapter] = new $class($settings);
        }

        return $adapters;
    }

    /**
     * Производим аутентификацию и авторизацию
     * @param Request $request
     * @return Response
     */
    public function authenticationAction(Request $request): Response
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $user = new Security($request->getSession());

            $isAuthenticationSuccess = $user->authentication(
                $request->request->get('login'),
                $request->request->get('password')
            );

            if ($isAuthenticationSuccess) {
                return $this->render(
                    'user/authentication_success',
                    ['user' => $user->getUser()]
                );
            }
            $error = 'Неправильный логин и/или пароль';
        }

        return $this->render(
            'user/authentication',
            [
                'error' => $error ?? '',
                'adapters' => $this->getAdapters()
            ]
        );
    }

    /**
     * Выходим из системы
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function logoutAction(Request $request): Response
    {
        (new Security($request->getSession()))->logout();

        return $this->redirect('index');
    }
}
