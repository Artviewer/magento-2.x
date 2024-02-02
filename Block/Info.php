<?php

declare(strict_types=1);

namespace RozetkaPay\RozetkaPay\Block;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;
use Magento\Sales\Helper\AdminTest;
use Magento\Setup\Module\Di\Code\Reader\Decorator\Area;

class Info extends ConfigurableInfo
{
    /**
     * Returns label
     *
     * @param string $field
     * @return Phrase
     */
    protected function getLabel($field): Phrase
    {
        return __($field);
    }

    protected function _prepareSpecificInformation($transport = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceHelper = $objectManager->create(\Magento\Framework\Pricing\Helper\Data::class);
        $transport = parent::_prepareSpecificInformation($transport);
        /**
         * @var \Magento\Sales\Model\Order\Payment\Interceptor
         */
        $info = $this->getInfo();

        $displayData = [];
        $frontDisplayData = [];

        $additionalInformation = $info->getAdditionalInformation();
        if (isset($additionalInformation['raw_details_info']['details'])) {

            $pageData = $additionalInformation['raw_details_info']['details'];

            if (isset($pageData['payment_id'])) {
                $displayData['Payment Id'] = $pageData['payment_id'];
            }

            if (isset($pageData['gateway_order_id'])) {
                $displayData['Gateway Order Id'] = $pageData['gateway_order_id'];
            }

            if (isset($pageData['transaction_id'])) {
                $displayData['Transaction Id'] = $pageData['transaction_id'];
                $frontDisplayData['Transaction Id'] = $pageData['transaction_id'];
            }

            if (isset($pageData['rrn'])) {
                $displayData['RRN'] = $pageData['rrn'];
            }

            if (isset($pageData['status'])) {
                $displayData['Status'] = $pageData['status'];
            }

            if (isset($pageData['status_description'])) {
                $displayData['Status description'] = $pageData['status_description'];
                $frontDisplayData['Status'] = $pageData['status_description'];
            }
        }


        if ($this->getArea() != 'adminhtml') {

            return $transport->setData($frontDisplayData);
        }

        return $transport->setData($displayData);
    }

    public function beforeToHtml(\Magento\Payment\Block\Info $subject)
    {
        if ($subject->getMethod()->getCode() == \Pay\PayGateway\Model\Ui\ConfigProvider::CODE) {
            $subject->setTemplate('RozetkaPay_RozetkaPay::info/default.phtml');
        } else {
            parent::_beforeToHtml($subject);
        }
    }
}
