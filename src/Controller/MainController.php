<?php

declare(strict_types = 1);

namespace Controller;

use Framework\BaseController;
use Model\Entity\Page;
use Symfony\Component\HttpFoundation\Response;

class MainController extends BaseController
{
    /**
     * Главная страница
     * @return Response
     */
    public function actionIndex(): Response
    {
        $content = (object)Page::$body;

        return $this->render(
            'main/index',
            [
                'description' => $content->description,
                'title' => $content->title,
            ]
        );
    }
}
