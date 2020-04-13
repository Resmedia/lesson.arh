<?php

namespace Service\Filter;

interface Filter
{
    /**
     * @param $current
     * @param $next
     * @return int
     */
    public function filter($current, $next): int;
}