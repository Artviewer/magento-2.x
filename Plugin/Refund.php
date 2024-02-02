<?php

declare(strict_types=1);

namespace RozetkaPay\RozetkaPay\Plugin;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Model\Service\CreditmemoService;
use RozetkaPay\RozetkaPay\Model\Gateway\Curl;
use Psr\Log\LoggerInterface;
use RozetkaPay\RozetkaPay\Logger\Logger as RozetkaInfoLogger;
use RozetkaPay\RozetkaPay\Model\Config\Provider as RozetkaConfig;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class Refund
{
    const REFUND_URL = 'api/payments/v1/refund';
    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var RozetkaInfoLogger
     */
    protected $rozetkaInfoLogger;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var RozetkaConfig
     */
    protected $rozetkaConfig;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param Curl $curl
     * @param RozetkaInfoLogger $rozetkaInfoLogger
     * @param LoggerInterface $logger
     * @param RozetkaConfig $rozetkaConfig
     * @param SerializerInterface $serializer
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Curl                $curl,
        RozetkaInfoLogger   $rozetkaInfoLogger,
        LoggerInterface     $logger,
        RozetkaConfig       $rozetkaConfig,
        SerializerInterface $serializer,
        OrderRepositoryInterface $orderRepository
    )
    {
        $this->curl = $curl;
        $this->rozetkaInfoLogger = $rozetkaInfoLogger;
        $this->logger = $logger;
        $this->rozetkaConfig = $rozetkaConfig;
        $this->serializer = $serializer;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param CreditmemoService $subject
     * @param CreditmemoInterface $result
     * @param CreditmemoInterface $creditmemo
     * @param bool $offlineRequested
     * @return CreditmemoInterface
     */
    public function afterRefund(
        CreditmemoService   $subject,
        CreditmemoInterface $result,
        CreditmemoInterface $creditmemo,
                            $offlineRequested = false
    ): CreditmemoInterface
    {
        try {
            $order = $creditmemo->getOrder();
            if ($order->getPayment()->getMethod() === 'rozetkapay') {
                $url = $this->rozetkaConfig->getApiUrl() . self::REFUND_URL;
                $data = $this->prepareRefund($creditmemo);
                $this->rozetkaInfoLogger->info('Refund Request: ', $data);
                $requestJson = $this->serializer->serialize($data);
                $headers = ['Content-Type' => 'application/json'];
                $responseJson = $this->curl->sendRequest('post', $url, $headers, $requestJson);
                $resultResponse = $this->serializer->unserialize($responseJson);
                $this->rozetkaInfoLogger->info('Refund Response: ', $resultResponse);
                $comment = 'Refund processed. API Response JSON: ' . $responseJson;
                $order->addCommentToStatusHistory($comment, false, false);
                $this->orderRepository->save($order);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        return $result;
    }

    private function prepareRefund($creditMemo)
    {
        $order = $creditMemo->getOrder();
        $currency = $order->getCurrency();
        $orderId = $order->getId();
        if ($this->rozetkaConfig->isSandboxMode()) {
            $currency = 'UAH';
            $orderId = $order->getId() . \RozetkaPay\RozetkaPay\Model\Gateway\Request\CreatePayment::SANDBOX_ORDER_MASK;
        }
        return [
            'external_id' => $orderId,
            'amount' => $creditMemo->getGrandTotal(),
            'currency' => $currency
        ];
    }
}
