<?php
/**
 * @category   Hps
 * @package    Hps_Transit
 * @copyright  Copyright (c) 2015 Heartland Payment Systems (https://www.magento.com)
 * @license    https://github.com/hps/transit-magento-extension/blob/master/LICENSE  Custom License
 */

class Hps_Transit_Block_Info extends Mage_Payment_Block_Info
{
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $data = array();
        $info = $this->getInfo();
        $additionalData = unserialize($info->getAdditionalData());
        $skipCC = isset($additionalData['giftcard_skip_cc']) && $additionalData['giftcard_skip_cc'];
        $gift = '';

        if (isset($additionalData['giftcard_number'])) {
            $gift = 'Gift Card' . (!$skipCC ? ' & ' : '');
        }

        $type = $gift;
        if (!$skipCC) {
            $cardType = isset($additionalData['cc_type'])
                ? $additionalData['cc_type']
                : ($info->getCcType() ? $info->getCcType() : '');
            $type .= sprintf(
                '%s ending with %s (%s/%s)',
                strtoupper($cardType),
                $info->getCcLast4(),
                $info->getCcExpMonth(),
                $info->getCcExpYear()
            );
        }

        $data[Mage::helper('payment')->__('Payment Type')] = $type;

        if ($this->isAdmin() && isset($additionalData['auth_code'])) {
            $data[Mage::helper('payment')->__('Authorization Code')] = $additionalData['auth_code'];
        }

        if ($this->isAdmin() && isset($additionalData['avs_response_code'])) {
            $data[Mage::helper('payment')->__('AVS Response')] = sprintf(
                '%s (%s)',
                $additionalData['avs_response_text'],
                $additionalData['avs_response_code']
            );
        }

        if ($this->isAdmin() && isset($additionalData['cvv_response_code'])) {
            $data[Mage::helper('payment')->__('CVV Response')] = sprintf(
                '%s (%s)',
                $additionalData['cvv_response_text'],
                $additionalData['cvv_response_code']
            );
        }

        return $transport->setData(array_merge($data, $transport->getData()));
    }

    protected function isAdmin()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return true;
        }

        if (Mage::getDesign()->getArea() == 'adminhtml') {
            return true;
        }

        return false;
    }
}
