<?php

declare(strict_types=1);

namespace RozetkaPay\RozetkaPay\Controller\Action;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\Result\JsonFactory;
use RozetkaPay\RozetkaPay\Model\Gateway\Request\CreatePayment;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

class GetRedirect implements HttpGetActionInterface
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var CreatePayment
     */
    private $createPayment;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param JsonFactory $jsonFactory
     * @param CreatePayment $createPayment
     * @param ManagerInterface $messageManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        JsonFactory $jsonFactory,
        CreatePayment $createPayment,
        ManagerInterface $messageManager,
        LoggerInterface $logger
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->createPayment = $createPayment;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        $data = [
            'status' => false,
            'redirect_url' => '/checkout/cart'
        ];

        try {
            $result = $this->createPayment->execute();
            if(!empty($result) && $result['is_success'] && isset($result['action']['type'])) {
                $data['status'] = true;
                $data['redirect_url'] = $result['action']['value'];
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->messageManager->addErrorMessage(__('Something went wrong.'));
        }

        $resultJson = $this->jsonFactory->create();
        $resultJson->setData($data);

        return $resultJson;
    }
}
