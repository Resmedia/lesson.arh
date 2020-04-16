<?php

declare(strict_types = 1);

namespace Framework;

use Exception;
use Service\User\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

abstract class BaseController
{
    /**
     * Отрисовка страницы
     * @param string $view
     * @param array $parameters
     * @return Response
     */
    protected function render($view, $parameters = [])
    {
        return $this->renderTemplate('layout/main', [
            'content' => $this->renderTemplate($view, $parameters)
        ]);
    }

    protected function renderTemplate(string $view, array $parameters = []): Response
    {
        $rootViewPath = Registry::getDataConfig('view.directory');
        $viewPath = $rootViewPath . $view . '.php';

        if (!file_exists($viewPath)) {
            return new Response('There is no view file ' . $view, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $path = function (string $name, array $parameters = []): string {
            return Registry::getRoute($name, $parameters);
        };

        function (string $view, array $parameters = []) use ($rootViewPath, $path): void {
            $parameters['isAuth'] = (new Security(new Session()))->isLogged();
            extract($parameters, EXTR_SKIP);

            try {
                include $rootViewPath . $view;
            } catch (\Throwable $e) {
                if (Registry::getDataConfig('environment') === 'dev') {
                    $error = $e->getMessage();
                    $trace = $e->getTraceAsString();
                    include str_replace('/', DIRECTORY_SEPARATOR, $rootViewPath . 'page/error500.php');
                } else {
                    throw $e;
                }
            }
        };

        ob_start();

        extract($parameters, EXTR_REFS);
        include $viewPath;

        $response = new Response(ob_get_clean());

        return $response;
    }

    /**
     * Перенаправление на другую страницу
     * @param string $name
     * @return RedirectResponse
     * @throws Exception
     */
    protected function redirect(string $name): RedirectResponse
    {
        $route = Registry::getRoute($name);
        return new RedirectResponse($route);
    }
}
