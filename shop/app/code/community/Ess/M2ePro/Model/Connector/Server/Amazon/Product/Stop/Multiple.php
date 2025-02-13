<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Server_Amazon_Product_Stop_Multiple
extends Ess_M2ePro_Model_Connector_Server_Amazon_Product_Requester
{
    // ########################################

    public function getCommand()
    {
        return array('product','update','entities');
    }

    // ########################################

    protected function getActionIdentifier()
    {
        if (isset($this->params['remove']) && (bool)$this->params['remove']) {
            return 'stop_and_remove';
        }
        return 'stop';
    }

    protected function getResponserModel()
    {
        return 'Amazon_Product_Stop_MultipleResponser';
    }

    protected function getListingsLogsCurrentAction()
    {
        if (isset($this->params['remove']) && (bool)$this->params['remove']) {
            return Ess_M2ePro_Model_Listing_Log::ACTION_STOP_AND_REMOVE_PRODUCT;
        }
        return Ess_M2ePro_Model_Listing_Log::ACTION_STOP_PRODUCT_ON_COMPONENT;
    }

    // ########################################

    protected function prepareListingsProducts($listingsProducts)
    {
        $tempListingsProducts = array();

        foreach ($listingsProducts as $listingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            if (!$listingProduct->isListed()) {

                if (!isset($this->params['remove']) || !(bool)$this->params['remove']) {
                    // Parser hack -> Mage::helper('M2ePro')->__('Item is not listed or not available');
                    $this->addListingsProductsLogsMessage($listingProduct, 'Item is not listed or not available',
                                                          Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                                          Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM);

                    continue;
                }
            }

            if ($listingProduct->isLockedObject(NULL) ||
                $listingProduct->isLockedObject('in_action') ||
                $listingProduct->isLockedObject($this->getActionIdentifier().'_action')) {

                // ->__('Another action is being processed. Try again when the action is completed.');
                $this->addListingsProductsLogsMessage(
                    $listingProduct, 'Another action is being processed. Try again when the action is completed.',
                    Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                    Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM
                );

                continue;
            }

            if (!$listingProduct->isListed() && isset($this->params['remove']) && (bool)$this->params['remove']) {
                $listingProduct->addData(array('status'=>Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED))->save();
                $listingProduct->deleteInstance();
                continue;
            }

            $tempListingsProducts[] = $listingProduct;
        }

        return $tempListingsProducts;
    }

    // ########################################

    protected function getRequestData()
    {
        $output = array(
            'items' => array()
        );

        foreach ($this->listingsProducts as $listingProduct) {

            /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            $requestData = Mage::getModel('M2ePro/Amazon_Connector_Product_Helper')
                                         ->getStopRequestData($listingProduct,$this->params);

            $listing = array(
                'id' => $listingProduct->getId(),
                'sku' => $requestData['sku'],
                'qty' => $requestData['qty']
            );

            $this->listingProductRequestsData[$listingProduct->getId()] = array(
                'native_data' => $requestData,
                'sended_data' => $listing
            );

            $output['items'][] = $listing;
        }

        return $output;
    }

    // ########################################
}