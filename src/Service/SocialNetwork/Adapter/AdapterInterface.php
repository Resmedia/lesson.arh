<?php

namespace Service\SocialNetwork\Adapter;

interface AdapterInterface
{
    /**
     * Authenticate and return bool result of authentication
     *
     * @return bool
     */
    public function authenticate();
}