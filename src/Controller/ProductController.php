<?php

declare(strict_types=1);

namespace Controller;

use Framework\BaseController;
use Service\Filter\NameFilter;
use Service\Filter\PriceFilter;
use Service\Order\Basket;
use Service\Product\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends BaseController
{
    /**
     * Информация о продукте
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function infoAction(Request $request, string $id): Response
    {
        $basket = (new Basket($request->getSession()));

        if ($request->isMethod(Request::METHOD_POST)) {
            $basket->addProduct((int)$request->request->get('product'));
        }

        $productInfo = (new ProductService())->getInfo((int)$id);

        if ($productInfo === null) {
            return $this->render('error404.html.php');
        }

        $isInBasket = $basket->isProductInBasket($productInfo->getId());

        return $this->render(
            'product/info.html.php',
            ['productInfo' => $productInfo, 'isInBasket' => $isInBasket]
        );
    }

    /**
     * Список всех продуктов
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request): Response
    {
        $sort = $request->query->get('sort');
        if ($sort && $sort == 'price') {
            $productList = (new ProductService())->getAll(new PriceFilter());
        }else if ($sort && $sort == 'name') {
            $productList = (new ProductService())->getAll(new NameFilter());
        } else {
            $productList = (new ProductService())->getAll();
        }

        return $this->render(
            'product/list.html.php',
            [
                'productList' => $productList,
            ]
        );
    }
}
