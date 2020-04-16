<?php

namespace Service\Filter;

use Model\Entity\Product;

class NameFilter implements Filter
{
    /**
     * @param Product $current
     * @param Product $next
     * @return int
     */
    public function filter($current, $next): int
    {
        return $current->getName() <=> $next->getName();
    }
}