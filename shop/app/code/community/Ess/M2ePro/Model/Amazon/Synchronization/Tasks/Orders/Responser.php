<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Orders_Responser
{
    protected $params = array();

    protected $mappingSettings = NULL;
    protected $synchronizationLog = NULL;
    protected $tempObjectsCache = array();

    /**
     * @var Ess_M2ePro_Model_Marketplace|null
     */
    protected $marketplace = NULL;

    /**
     * @var Ess_M2ePro_Model_Account|null
     */
    protected $account = NULL;

    // ########################################

    public function initialize(array $params = array(),
                               Ess_M2ePro_Model_Marketplace $marketplace = NULL,
                               Ess_M2ePro_Model_Account $account = NULL)
    {
        $this->params = $params;
        $this->marketplace = $marketplace;
        $this->account = $account;
    }

    // ########################################

    public function unsetLocks($hash, $fail = false, $message = NULL)
    {
        /** @var $lockItem Ess_M2ePro_Model_LockItem */
        $lockItem = Mage::getModel('M2ePro/LockItem');

        $tempNick = Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Orders::LOCK_ITEM_PREFIX;
        $tempNick .= '_'.$this->params['account_id'].'_'.$this->params['marketplace_id'];

        $lockItem->setNick($tempNick);
        $lockItem->remove();

        $this->getAccount()->deleteObjectLocks(NULL,$hash);
        $this->getAccount()->deleteObjectLocks('synchronization',$hash);
        $this->getAccount()->deleteObjectLocks('synchronization_amazon',$hash);
        $this->getAccount()->deleteObjectLocks(
            Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Orders::LOCK_ITEM_PREFIX,
            $hash
        );

        $this->getMarketplace()->deleteObjectLocks(NULL,$hash);
        $this->getMarketplace()->deleteObjectLocks('synchronization',$hash);
        $this->getMarketplace()->deleteObjectLocks('synchronization_amazon',$hash);
        $this->getMarketplace()->deleteObjectLocks(
            Ess_M2ePro_Model_Amazon_Synchronization_Tasks_Orders::LOCK_ITEM_PREFIX,
            $hash
        );

        $fail && $this->getSynchLogModel()->addMessage(Mage::helper('M2ePro')->__($message),
                                                       Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                                                       Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH);
    }

    public function processSucceededResponseData($receivedOrders)
    {
        Mage::helper('M2ePro/Exception')->setFatalErrorHandler();

        try {

            $account = $this->getAccount();

            if (!$account->getChildObject()->isOrdersModeEnabled()) {
                return;
            }

            $orders = array();

            $lastOrderUpdateDate = NULL;
            $ordersLastSynchronization = $account->getData('orders_last_synchronization');

            // Create m2e orders
            //---------------------------
            foreach ($receivedOrders as $orderData) {
                $currentOrderUpdateDate = $orderData['purchase_update_date'];
                is_null($lastOrderUpdateDate) && $lastOrderUpdateDate = $orderData['purchase_update_date'];

                if (strtotime($lastOrderUpdateDate) < strtotime($currentOrderUpdateDate)) {
                    $lastOrderUpdateDate = $currentOrderUpdateDate;
                }

                /** @var $orderBuilder Ess_M2ePro_Model_Amazon_Order_Builder */
                $orderBuilder = Mage::getModel('M2ePro/Amazon_Order_Builder');
                $orderBuilder->setAccount($account)
                    ->initialize($orderData);

                $order = $orderBuilder->process();

                if (!$order) {
                    continue;
                }

                $orders[] = $order;
            }
            //---------------------------

            if (is_null($ordersLastSynchronization) && is_null($lastOrderUpdateDate)) {
                $lastOrderUpdateDate = new DateTime('now', new DateTimeZone('UTC'));
                $lastOrderUpdateDate = $lastOrderUpdateDate->format('Y-m-d H:i:s');
            }

            if (!is_null($lastOrderUpdateDate)) {
                $account->setData('orders_last_synchronization', $lastOrderUpdateDate)->save();
            }

            if (count($orders) == 0) {
                return;
            }

            // Create magento orders
            //---------------------------
            foreach ($orders as $order) {
                /** @var $order Ess_M2ePro_Model_Order */
                $order->createMagentoOrder();
                $order->updateMagentoOrderStatus();
                $order->createInvoice();
                $order->createShipment();
            }
            //---------------------------

        } catch (Exception $exception) {

            Mage::helper('M2ePro/Exception')->process($exception,true);

            $this->getSynchLogModel()->addMessage(Mage::helper('M2ePro')->__($exception->getMessage()),
                Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH);
        }
    }

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Account
     */
    protected function getAccount()
    {
        return $this->account;
    }

    /**
     * @return Ess_M2ePro_Model_Marketplace
     */
    protected function getMarketplace()
    {
        return $this->marketplace;
    }

    //-----------------------------------------

    protected function getSynchLogModel()
    {
        if (!is_null($this->synchronizationLog)) {
            return $this->synchronizationLog;
        }

        /** @var $runs Ess_M2ePro_Model_Synchronization_Run */
        $runs = Mage::getModel('M2ePro/Synchronization_Run');
        $runs->start(Ess_M2ePro_Model_Synchronization_Run::INITIATOR_UNKNOWN);
        $runsId = $runs->getLastId();
        $runs->stop();

        /** @var $logs Ess_M2ePro_Model_Synchronization_Log */
        $logs = Mage::getModel('M2ePro/Synchronization_Log');
        $logs->setSynchronizationRun($runsId);
        $logs->setComponentMode(Ess_M2ePro_Helper_Component_Amazon::NICK);
        $logs->setInitiator(Ess_M2ePro_Model_Synchronization_Run::INITIATOR_UNKNOWN);
        $logs->setSynchronizationTask(Ess_M2ePro_Model_Synchronization_Log::SYNCH_TASK_ORDERS);

        $this->synchronizationLog = $logs;

        return $this->synchronizationLog;
    }

    // ########################################
}