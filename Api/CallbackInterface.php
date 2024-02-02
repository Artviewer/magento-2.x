<?php

namespace RozetkaPay\RozetkaPay\Api;

interface CallbackInterface
{
    /**
     * @return mixed
     */
    public function execute();
}
