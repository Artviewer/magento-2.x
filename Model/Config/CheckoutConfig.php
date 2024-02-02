<?php

declare(strict_types=1);

namespace RozetkaPay\RozetkaPay\Model\Config;

use Magento\Checkout\Model\ConfigProviderInterface;

class CheckoutConfig implements ConfigProviderInterface
{
    /**
     * @var Provider
     */
    private $configProvider;

    /**
     * @param Provider $configProvider
     */
    public function __construct(Provider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @return array
     */
    public function getConfig() {
        $additionalVariables = [];
        if($this->configProvider->isActive()) {
            $data = [
                'sandbox' => $this->configProvider->isSandboxMode(),
                'description' => $this->configProvider->getDescription()
            ];
            $additionalVariables['rozetkapay'] = $data;
        }

        return $additionalVariables;
    }
}
