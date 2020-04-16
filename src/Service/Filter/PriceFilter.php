<?php

namespace Service\Filter;

use Model\Entity\Product;

class PriceFilter implements Filter
{
    /**
     * @param Product $current
     * @param Product $next
     * @return int
     */
    public function filter($current, $next): int
    {
        return $current->getPrice() <=> $next->getPrice();
    }
}