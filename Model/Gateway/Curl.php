<?php

declare(strict_types=1);

namespace RozetkaPay\RozetkaPay\Model\Gateway;

use Magento\Framework\HTTP\Client\Curl as FrameworkCurl;
use RozetkaPay\RozetkaPay\Model\Config\Provider;

class Curl
{
    /**
     * @var FrameworkCurl|Magento\Framework\HTTP\Client\Curl
     */
    private $curl;

    /**
     * @var Provider
     */
    protected $rozetkaConfig;

    /**
     * @param FrameworkCurl $curl
     * @param Provider $rozetkaConfig
     */
    public function __construct(
        FrameworkCurl $curl,
        Provider $rozetkaConfig
    ) {
        $this->curl = $curl;
        $this->rozetkaConfig = $rozetkaConfig;
    }

    /**
     * @param $method
     * @param $url
     * @param $headers
     * @param $data
     * @return string
     */
    public function sendRequest($method, $url, $headers, $data)
    {
        if(!empty($headers)) {
            $this->curl->setHeaders($headers);
        }
        $userName = $this->rozetkaConfig->getShopId();
        $password = $this->rozetkaConfig->getShopPass();
        $this->curl->setCredentials($userName, $password);
        if($method === 'post') {
            $this->curl->post($url, $data);
        } elseif ($method === 'get') {
            $this->curl->get($url);
        }

        return $this->curl->getBody();
    }
}
