<?php

declare(strict_types=1);

namespace RozetkaPay\RozetkaPay\Model\Gateway\Request;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use RozetkaPay\RozetkaPay\Logger\Logger as RozetkaInfoLogger;
use RozetkaPay\RozetkaPay\Model\Config\Provider;
use RozetkaPay\RozetkaPay\Model\Gateway\Curl;
use RozetkaPay\RozetkaPay\Model\Gateway\GatewayAbstract;
use Magento\Sales\Model\Order;

class CreatePayment extends GatewayAbstract
{
    const CREATE_PAYMENT = 'api/payments/v1/new';

    const CALLBACK_URL = 'rest/V1/rozetkapay/callback';

    const SANDBOX_ORDER_MASK = '999333777';

    const SUCCESS = 'checkout/onepage/success';

    /**
     * @return array|bool|float|int|string|void|null
     */
    public function execute()
    {
        try {
            $url = $this->rozetkaConfig->getApiUrl() . self::CREATE_PAYMENT;
            $order = $this->checkoutSession->getLastRealOrder();
            $data = $this->prepareData($order);
            $this->rozetkaLogger->info('CreatePayment Request: ', $data);
            $requestJson = $this->serializer->serialize($data);
            $headers = ['Content-Type' => 'application/json'];
            $responseJson = $this->curl->sendRequest('post', $url, $headers, $requestJson);
            $result = $this->serializer->unserialize($responseJson);
            $payment = $order->getPayment();
            $payment->setAdditionalInformation('transaction_id', $result['id']);
            $payment->save();
            $order->setState(Order::STATE_PENDING_PAYMENT)->setStatus(Order::STATE_PENDING_PAYMENT);
            $this->orderRepository->save($order);
            $this->rozetkaLogger->info('CreatePayment Response: ', $result);

            return $result;
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    private function prepareData($order)
    {
        $address = $order->getBillingAddress();
        $customerData = [
            'address' => implode(',', $address->getStreet()),
            'city' => $address->getCity(),
            'country' => $address->getCountryId(),
            'email' => $address->getEmail(),
            'first_name' => $address->getFirstName(),
            'last_name' => $address->getLastName(),
            'phone' => $address->getTelephone()
        ];
        if ($customer = $order->getCustomer()) {
            $customerData['external_id'] = $customer->getId();
        }
        $currency = $order->getCurrency();
        $orderId = $order->getId();
        if($this->rozetkaConfig->isSandboxMode()) {
            $currency = 'UAH';
            $orderId = $order->getId() . self::SANDBOX_ORDER_MASK;
        }
        $data = [
            'amount' => $order->getGrandTotal(),
            'callback_url' => $this->urlBuilder->getDirectUrl(self::CALLBACK_URL),
            'result_url' => $this->urlBuilder->getUrl(self::SUCCESS),
            'currency' => $currency,
            'customer' => $customerData,
            'external_id' => $orderId,
            'mode' => 'hosted'
        ];

        return $data;
    }
}
