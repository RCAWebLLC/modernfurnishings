<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Defaults_UpdateListingsProducts_ServerResponser extends
                                                Ess_M2ePro_Model_Connector_Server_Amazon_Inventory_Get_ItemsResponser
{
    // ########################################

    protected function unsetLocks($fail = false, $message = NULL)
    {
        /** @var $tempModel Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Defaults_UpdateListingsProducts_Responser */
        $tempModel = Mage::getModel('M2ePro/Amazon_Synchronization_Tasks_Defaults_UpdateListingsProducts_Responser');
        $tempModel->initialize($this->params,$this->getMarketplace(),$this->getAccount());
        $tempModel->unsetLocks($this->hash, $fail, $message);
    }

    protected function processResponseData($response)
    {
        $receivedItems = parent::processResponseData($response);

        /** @var $tempModel Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Defaults_UpdateListingsProducts_Responser */
        $tempModel = Mage::getModel('M2ePro/Amazon_Synchronization_Tasks_Defaults_UpdateListingsProducts_Responser');
        $tempModel->initialize($this->params,$this->getMarketplace(),$this->getAccount());
        $tempModel->processSucceededResponseData($receivedItems);
    }

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Account
     */
    protected function getAccount()
    {
        return $this->getObjectByParam('Account','account_id');
    }

    /**
     * @return Ess_M2ePro_Model_Marketplace
     */
    protected function getMarketplace()
    {
        return $this->getObjectByParam('Marketplace','marketplace_id');
    }

    // ########################################
}