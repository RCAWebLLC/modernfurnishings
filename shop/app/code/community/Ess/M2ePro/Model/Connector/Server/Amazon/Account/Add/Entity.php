<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class   Ess_M2ePro_Model_Connector_Server_Amazon_Account_Add_Entity
extends Ess_M2ePro_Model_Connector_Server_Amazon_Requester
{
    // ########################################

    protected function getCommand()
    {
        return array('account','add','entity');
    }

    // ########################################

    protected function getResponserModel()
    {
        return 'Amazon_Account_Add_EntityResponser';
    }

    protected function getResponserParams()
    {
        return array(
            'account_id' => $this->account->getId(),
            'marketplace_id' => $this->params['marketplace_id'],
            'user_merchant_id' => $this->params['user_merchant_id'],
            'related_store_id' => $this->params['related_store_id']
        );
    }

    // ########################################

    protected function setLocks($hash)
    {
        $this->account->addObjectLock(NULL,$hash);
        $this->account->addObjectLock('server_synchronize',$hash);
        $this->account->addObjectLock('adding_to_server',$hash);
    }

    // ########################################

    protected function getRequestData()
    {
        /** @var $marketplaceObject Ess_M2ePro_Model_Marketplace */
        $marketplaceObject = Mage::helper('M2ePro/Component_Amazon')
                ->getObject('Marketplace',$this->params['marketplace_id']);

        return array(
            'title' => $this->account->getTitle(),
            'user_merchant_id' => $this->params['user_merchant_id'],
            'marketplace_id' => $marketplaceObject->getNativeId()
        );
    }

    // ########################################
}