<?php

declare(strict_types=1);

namespace RozetkaPay\RozetkaPay\Model\Gateway;

use RozetkaPay\RozetkaPay\Model\Config\Provider;
use Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Api\OrderRepositoryInterface;
use RozetkaPay\RozetkaPay\Logger\Logger as RozetkaInfoLogger;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

abstract class GatewayAbstract
{
    /**
     * @var Provider
     */
    protected $rozetkaConfig;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \RozetkaPay\RozetkaPay\Model\Gateway\Curl
     */
    protected $curl;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var RozetkaInfoLogger
     */
    protected $rozetkaLogger;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param Provider $rozetkaConfig
     * @param RequestInterface $request
     * @param Session $checkoutSession
     * @param OrderRepositoryInterface $orderRepository
     * @param Curl $curl
     * @param SerializerInterface $serializer
     * @param RozetkaInfoLogger $rozetkaLogger
     * @param UrlInterface $urlBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        Provider $rozetkaConfig,
        RequestInterface $request,
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        Curl $curl,
        SerializerInterface $serializer,
        RozetkaInfoLogger $rozetkaLogger,
        UrlInterface $urlBuilder,
        LoggerInterface $logger
    )
    {
        $this->rozetkaConfig = $rozetkaConfig;
        $this->request = $request;
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->curl = $curl;
        $this->serializer = $serializer;
        $this->rozetkaLogger = $rozetkaLogger;
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger;
    }
}
