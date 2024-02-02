<?php

namespace RozetkaPay\RozetkaPay\Model\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class OrderRequest implements BuilderInterface
{
    public function build(array $buildSubject)
    {
        return $buildSubject;
    }
}
