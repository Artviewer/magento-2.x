<?php

declare(strict_types=1);

namespace RozetkaPay\RozetkaPay\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Provider
{
    const ACTIVE_CONFIG_PATH = 'payment/rozetkapay/active';
    const SANDBOX_CONFIG_PATH = 'payment/rozetkapay/sandbox';
    const TITLE_CONFIG_PATH = 'payment/rozetkapay/title';
    const DESCRIPTION_CONFIG_PATH = 'payment/rozetkapay/description';
    const API_URL_CONFIG_PATH = 'payment/rozetkapay/api_url';
    const SANDBOX_API_URL_CONFIG_PATH = 'payment/rozetkapay/sandbox_api_url';
    const SHOP_ID_CONFIG_PATH = 'payment/rozetkapay/shop_id';
    const SANDBOX_SHOP_ID_CONFIG_PATH = 'payment/rozetkapay/sandbox_shop_id';
    const SHOP_PASS_CONFIG_PATH = 'payment/rozetkapay/shop_pass';
    const SANDBOX_SHOP_PASS_CONFIG_PATH = 'payment/rozetkapay/sandbox_shop_pass';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var bool
     */
    private $sandboxMode = false;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $path
     * @param $scopeValue
     * @param $scopeType
     * @return mixed
     */
    public function getConfig($path, $scopeValue = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeValue);
    }

    /**
     * @return StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->storeManager;
    }

    /**
     * @return mixed
     */
    public function isActive()
    {
        return $this->getConfig(self::ACTIVE_CONFIG_PATH);
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->getConfig(self::TITLE_CONFIG_PATH);
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->getConfig(self::DESCRIPTION_CONFIG_PATH);
    }

    /**
     * @return mixed
     */
    public function isSandboxMode()
    {
        if(!$this->sandboxMode) {
            $this->sandboxMode = $this->getConfig(self::SANDBOX_CONFIG_PATH);
        }

        return $this->sandboxMode;
    }

    /**
     * @return mixed
     */
    public function getApiUrl()
    {
        if($this->isSandboxMode()) {
            return $this->getConfig(self::SANDBOX_API_URL_CONFIG_PATH);
        }

        return $this->getConfig(self::API_URL_CONFIG_PATH);
    }

    /**
     * @return mixed
     */
    public function getShopId()
    {
        if($this->isSandboxMode()) {
            return $this->getConfig(self::SANDBOX_SHOP_ID_CONFIG_PATH);
        }

        return $this->getConfig(self::SHOP_ID_CONFIG_PATH);
    }

    /**
     * @return mixed
     */
    public function getShopPass()
    {
        if($this->isSandboxMode()) {
            return $this->getConfig(self::SANDBOX_SHOP_PASS_CONFIG_PATH);
        }

        return $this->getConfig(self::SHOP_PASS_CONFIG_PATH);
    }
}
