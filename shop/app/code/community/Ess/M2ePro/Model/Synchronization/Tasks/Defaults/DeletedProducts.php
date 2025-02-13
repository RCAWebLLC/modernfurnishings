<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Synchronization_Tasks_Defaults_DeletedProducts extends Ess_M2ePro_Model_Synchronization_Tasks
{
    const PERCENTS_START = 0;
    const PERCENTS_END = 10;
    const PERCENTS_INTERVAL = 10;

    //####################################

    public function process()
    {
        // PREPARE SYNCH
        //---------------------------
        $this->prepareSynch();
        //---------------------------

        // RUN SYNCH
        //---------------------------
        $this->execute();
        //---------------------------

        // CANCEL SYNCH
        //---------------------------
        $this->cancelSynch();
        //---------------------------
    }

    //####################################

    private function prepareSynch()
    {
        $this->_lockItem->activate();

        $this->_profiler->addEol();
        $this->_profiler->addTitle('Deleted Products Actions');
        $this->_profiler->addTitle('--------------------------');
        $this->_profiler->addTimePoint(__CLASS__,'Total time');
        $this->_profiler->increaseLeftPadding(5);

        $this->_lockItem->setPercents(self::PERCENTS_START);
        $this->_lockItem->setStatus(
            Mage::helper('M2ePro')->__('The "Deleted Products" action is started. Please wait...')
        );
    }

    private function cancelSynch()
    {
        $this->_lockItem->setPercents(self::PERCENTS_END);
        $this->_lockItem->setStatus(
            Mage::helper('M2ePro')->__('The "Deleted Products" action is finished. Please wait...')
        );

        $this->_profiler->decreaseLeftPadding(5);
        $this->_profiler->addTitle('--------------------------');
        $this->_profiler->saveTimePoint(__CLASS__);

        $this->_lockItem->activate();
    }

    //####################################

    private function execute()
    {
        // Prepare last time
        $this->prepareLastTime();

        // Check locked last time
        if ($this->isLockedLastTime()) {
            return;
        }

        $collection = Mage::getModel('M2ePro/Listing_Product')->getCollection();

        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $collection->getSelect()->columns('product_id');
        $collection->getSelect()->distinct(true);

        $entityTableName = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity');
        $collection->getSelect()->joinLeft(
            array('cpe'=>$entityTableName), '(cpe.entity_id = `main_table`.product_id)',array('entity_id')
        );

        $tempProductsIds = array();
        $rows = $collection->toArray();

        foreach ($rows['items'] as $row) {

            if (in_array((int)$row['product_id'],$tempProductsIds)) {
                continue;
            }
            if (!is_null($row['entity_id'])) {
                continue;
            }

            $tempProductsIds[] = (int)$row['product_id'];
            Mage::getModel('M2ePro/Listing')->deleteProduct((int)$row['product_id']);
        }
    }

    //####################################

    private function prepareLastTime()
    {
        $lastTime = $this->getCheckLastTime();
        if (empty($lastTime)) {
            $lastTime = new DateTime('now', new DateTimeZone('UTC'));
            $lastTime->modify("-1 year");
            $this->setCheckLastTime($lastTime);
        }
    }

    private function isLockedLastTime()
    {
        $lastTime = strtotime($this->getCheckLastTime());

        $tempGroup = '/synchronization/settings/defaults/deleted_products/';
        $interval = (int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue($tempGroup,'interval');

        if ($lastTime + $interval > Mage::helper('M2ePro')->getCurrentGmtDate(true)) {
            return true;
        }

        return false;
    }

    private function getCheckLastTime()
    {
        $tempGroup = '/synchronization/settings/defaults/deleted_products/';
        return Mage::helper('M2ePro/Module')->getConfig()->getGroupValue($tempGroup,'last_time');
    }

    private function setCheckLastTime($time)
    {
        if ($time instanceof DateTime) {
            $time = (int)$time->format('U');
        }
        if (is_int($time)) {
            $time = strftime("%Y-%m-%d %H:%M:%S", $time);
        }
        $tempGroup = '/synchronization/settings/defaults/deleted_products/';
        Mage::helper('M2ePro/Module')->getConfig()->setGroupValue($tempGroup,'last_time',$time);
    }

    //####################################
}