<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Ebay_Synchronization_Tasks_Templates extends Ess_M2ePro_Model_Ebay_Synchronization_Tasks
{
    // ->__('Task "Templates Synchronization" has completed with errors. View %sl%listings log%el% for details.');
    // ->__('Task "Templates Synchronization" has completed with warnings. View %sl%listings log%el% for details.');

    //####################################

    const PERCENTS_START = 0;
    const PERCENTS_END = 100;
    const PERCENTS_INTERVAL = 100;

    //####################################

    public function process()
    {
        // Check tasks config mode
        //-----------------------------
        $config = Mage::helper('M2ePro/Module')->getConfig();

        $startMode = (bool)(int)$config->getGroupValue('/ebay/synchronization/settings/templates/start/','mode');
        $endMode = (bool)(int)$config->getGroupValue('/ebay/synchronization/settings/templates/end/','mode');
        $reviseMode = (bool)(int)$config->getGroupValue('/ebay/synchronization/settings/templates/revise/','mode');
        $relistMode = (bool)(int)$config->getGroupValue('/ebay/synchronization/settings/templates/relist/','mode');
        $stopMode = (bool)(int)$config->getGroupValue('/ebay/synchronization/settings/templates/stop/','mode');

        if (!$startMode && !$endMode && !$reviseMode && !$relistMode && !$stopMode) {
            return false;
        }
        //-----------------------------

        // PREPARE SYNCH
        //---------------------------
        $this->prepareSynch();
        $this->createRunnerActions();
        //---------------------------

        // GET TEMPLATES
        //---------------------------
        $this->_profiler->addEol();
        $synchronizations = $this->getTemplatesWithListings();
        Mage::helper('M2ePro')->setGlobalValue('synchTemplatesArray',$synchronizations);

        $this->_lockItem->setPercents(self::PERCENTS_START + 5);
        $this->_lockItem->activate();
        //---------------------------

        // RUN CHILD SYNCH
        //---------------------------
        if ($startMode) {
            $tempSynch = new Ess_M2ePro_Model_Ebay_Synchronization_Tasks_Templates_Start();
            $tempSynch->process();
        }

        if ($endMode) {
            $tempSynch = new Ess_M2ePro_Model_Ebay_Synchronization_Tasks_Templates_End();
            $tempSynch->process();
        }

        if ($reviseMode) {
            $tempSynch = new Ess_M2ePro_Model_Ebay_Synchronization_Tasks_Templates_Revise();
            $tempSynch->process();
        }

        if ($relistMode) {
            $tempSynch = new Ess_M2ePro_Model_Ebay_Synchronization_Tasks_Templates_Relist();
            $tempSynch->process();
        }

        if ($stopMode) {
            $tempSynch = new Ess_M2ePro_Model_Ebay_Synchronization_Tasks_Templates_Stop();
            $tempSynch->process();
        }
        //---------------------------

        // UNSET TEMPLATES
        //---------------------------
        Mage::helper('M2ePro')->unsetGlobalValue('synchTemplatesArray');
        //---------------------------

        // CANCEL SYNCH
        //---------------------------
        $this->executeRunnerActions();
        $this->cancelSynch();
        //---------------------------
    }

    //####################################

    private function prepareSynch()
    {
        $this->_lockItem->activate();
        $this->_logs->setSynchronizationTask(Ess_M2ePro_Model_Synchronization_Log::SYNCH_TASK_TEMPLATES);

        if (count(Mage::helper('M2ePro/Component')->getActiveComponents()) > 1) {
            $componentName = Ess_M2ePro_Helper_Component_Ebay::TITLE.' ';
        } else {
            $componentName = '';
        }

        $this->_profiler->addEol();
        $this->_profiler->addTitle($componentName.'Templates Synchronization');
        $this->_profiler->addTitle('--------------------------');
        $this->_profiler->addTimePoint(__CLASS__,'Total time');
        $this->_profiler->increaseLeftPadding(5);

        $this->_lockItem->setTitle(Mage::helper('M2ePro')->__($componentName.'Templates Synchronization'));
        $this->_lockItem->setPercents(self::PERCENTS_START);
        $this->_lockItem->setStatus(
            Mage::helper('M2ePro')->__('Task "Templates Synchronization" is started. Please wait...')
        );
    }

    private function cancelSynch()
    {
        $this->_lockItem->setPercents(self::PERCENTS_END);
        $this->_lockItem->setStatus(
            Mage::helper('M2ePro')->__('Task "Templates Synchronization" is finished. Please wait...')
        );

        $this->_profiler->decreaseLeftPadding(5);
        $this->_profiler->addEol();
        $this->_profiler->addTitle('--------------------------');
        $this->_profiler->saveTimePoint(__CLASS__);

        $this->_logs->setSynchronizationTask(Ess_M2ePro_Model_Synchronization_Log::SYNCH_TASK_UNKNOWN);
        $this->_lockItem->activate();
    }

    //####################################

    private function createRunnerActions()
    {
        $runnerActionsModel = Mage::getModel('M2ePro/Ebay_Synchronization_RunnerActions');
        $runnerActionsModel->removeAllProducts();
        Mage::helper('M2ePro')->setGlobalValue('synchRunnerActions',$runnerActionsModel);
        $this->_runnerActions = $runnerActionsModel;
    }

    private function executeRunnerActions()
    {
        $this->_profiler->addEol();
        $this->_profiler->addTimePoint(__METHOD__,'Apply products changes on eBay');

        $result = $this->_runnerActions->execute($this->_lockItem,
                                                 self::PERCENTS_START + 60,
                                                 self::PERCENTS_END);

        $startLink = '<a href="route:*/adminhtml_log/listing;back:*/adminhtml_log/synchronization">';
        $endLink = '</a>';

        if ($result == Ess_M2ePro_Model_Connector_Server_Ebay_Item_Abstract::STATUS_ERROR) {

            $tempString = Mage::getModel('M2ePro/Log_Abstract')->encodeDescription(
                'Task "Templates Synchronization" has completed with errors. View %sl%listings log%el% for details.',
                array('!sl'=>$startLink,'!el'=>$endLink)
            );
            $this->_logs->addMessage($tempString,
                                     Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                     Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH);
            $this->_profiler->addTitle('Updating products on eBay ended with errors.');
        }

        if ($result == Ess_M2ePro_Model_Connector_Server_Ebay_Item_Abstract::STATUS_WARNING) {

            $tempString = Mage::getModel('M2ePro/Log_Abstract')->encodeDescription(
                'Task "Templates Synchronization" has completed with warnings. View %sl%listings log%el% for details.',
                array('!sl'=>$startLink,'!el'=>$endLink)
            );
            $this->_logs->addMessage($tempString,
                                     Ess_M2ePro_Model_Log_Abstract::TYPE_WARNING,
                                     Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM);
            $this->_profiler->addTitle('Updating products on eBay ended with warnings.');
        }

        $this->_runnerActions->removeAllProducts();
        Mage::helper('M2ePro')->unsetGlobalValue('synchRunnerActions');
        $this->_runnerActions = NULL;

        $this->_profiler->saveTimePoint(__METHOD__);
    }

    //####################################

    private function getTemplatesWithListings()
    {
        $this->_profiler->addTimePoint(__METHOD__,'Get templates with listings');

        // Get synchronizations array
        //--------------------------
        $synchronizationObjects = Mage::helper('M2ePro/Component_Ebay')->getModel('Template_Synchronization')
                                            ->getCollection()->getItems();

        if (count($synchronizationObjects) <= 0) {
            return array();
        }
        //--------------------------

        // Get synchronizations
        //--------------------------
        $synchronizations = array();

        foreach ($synchronizationObjects as $synchronizationObject) {

            /** @var $synchronizationObject Ess_M2ePro_Model_Template_Synchronization */

            $synchronizationTemp = array(
                'instance' => $synchronizationObject,
                'listings' => array()
            );

            // Get listings
            //--------------------------
            $listingObjects = Mage::helper('M2ePro/Component_Ebay')->getModel('Listing')->getCollection()
                ->addFieldToFilter('template_synchronization_id', $synchronizationObject->getId())
                ->getItems();

            if (count($listingObjects) <= 0) {
                continue;
            }

            foreach ($listingObjects as $listingObject) {

                /** @var $listingObject Ess_M2ePro_Model_Listing */
                $listingObject->setSynchronizationTemplate($synchronizationObject);
                $synchronizationTemp['listings'][] = $listingObject;
            }
            //--------------------------

            if (count($synchronizationTemp['listings']) != 0) {
                $synchronizations[] = $synchronizationTemp;
            }
        }
        //--------------------------

        $this->_profiler->saveTimePoint(__METHOD__);

        return $synchronizations;
    }

    //####################################
}