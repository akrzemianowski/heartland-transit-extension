<?php

use GlobalPayments\Api\ServicesContainer;

class Hps_Transit_Model_Observer
{
    const CONFIG_FORMAT = 'payment/hps_transit/%s';

    public function requestTransactionKey()
    {
        if (!empty(Mage::helper('hps_transit/data')->getConfig('transaction_key'))) {
            return;
        }

        Mage::helper('hps_transit/data')->configureSDK();

        $response = ServicesContainer::instance()->getClient()->getTransactionKey();

        Mage::getConfig()->saveConfig(sprintf(self::CONFIG_FORMAT, 'transaction_key'), $response->transactionKey);
        Mage::getConfig()->saveConfig(sprintf(self::CONFIG_FORMAT, 'user_id'), null);
        Mage::getConfig()->saveConfig(sprintf(self::CONFIG_FORMAT, 'password'), null);
    }
}