<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_File
 */
class Amasty_File_Model_Stat extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
       $this->_init('amfile/stat','id');
    }

    public function saveStat($data)
    {
        $store = Mage::app()->getStore()->getStoreId();

        $stat = Mage::getModel('amfile/stat')
            ->setData($data)
            ->setStore($store)
            ->setDate(Mage::getModel('core/date')->gmtDate())
        ;

        if (Mage::getSingleton('customer/session')->isLoggedIn())
        {
            $stat->setCustomerId(Mage::getSingleton('customer/session')->getCustomer()->getId());
        }

        $stat->save();
    }
}
