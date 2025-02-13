<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Magento_Shipping
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'm2eproshipping';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $shippingData = Mage::helper('M2ePro')->getGlobalValue('shipping_data');

        if (!$shippingData) {
            // No data in session - this is frontend.
            if (!Mage::getStoreConfig('carriers/' . $this->_code . '/show_on_frontend')) {
                return false;
            }

            $shippingData = array(
                'carrier_title' => Mage::helper('M2ePro')->__('N/A'),
                'shipping_method' => Mage::helper('M2ePro')->__('M2E Pro Shipping Method'),
                'shipping_price' => 0
            );
        }

        $result = Mage::getModel('shipping/rate_result');
        $method = Mage::getModel('shipping/rate_result_method');

        $method->setCarrier($this->_code);
        $method->setMethod($this->_code);

        // eBay/Amazon Shipping
        $method->setCarrierTitle($shippingData['carrier_title']);
        $method->setMethodTitle($shippingData['shipping_method']);

        $method->setCost($shippingData['shipping_price']);
        $method->setPrice($shippingData['shipping_price']);

        $result->append($method);

        return $result;
    }

    public function checkAvailableShipCountries(Mage_Shipping_Model_Rate_Request $request)
    {
        $shippingData = Mage::helper('M2ePro')->getGlobalValue('shipping_data');

        if (!$shippingData) {
            // No data in session - this is frontend.
            if (!Mage::getStoreConfig('carriers/' . $this->_code . '/show_on_frontend')) {
                return false;
            }
        }

        return parent::checkAvailableShipCountries($request);
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array($this->_code => $this->getConfigData('name'));
    }

    /**
     * Check if carrier has shipping tracking option available
     *
     * @return boolean
     */
    public function isTrackingAvailable()
    {
        return false;
    }
}