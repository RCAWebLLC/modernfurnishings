<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Ebay_Synchronization_RunnerActions
{
    private $_actionsProducts = array();

    // ########################################

    public function setProduct(Ess_M2ePro_Model_Listing_Product $listingProductInstance,
                               $action, array $params = array())
    {
        $newListingsProductId = $listingProductInstance->getId();
        $params['status_changer'] = Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_SYNCH;

        // Check same product and action set before
        //----------------------------------
        $tempExistItem = NULL;

        if (isset($this->_actionsProducts[$newListingsProductId])) {

            $tempExistItem = $this->_actionsProducts[$newListingsProductId];

            if ($tempExistItem['action'] == $action) {

                foreach ($params as $tempParamKey => $tempParamValue) {

                    if (isset($tempExistItem['params'][$tempParamKey]) &&
                        is_array($tempExistItem['params'][$tempParamKey]) &&
                        is_array($tempParamValue)) {

                        $this->_actionsProducts[$newListingsProductId]['params'][$tempParamKey] =
                            array_merge($tempExistItem['params'][$tempParamKey],$tempParamValue);
                    } else {
                        $this->_actionsProducts[$newListingsProductId]['params'][$tempParamKey] = $tempParamValue;
                    }
                }

                return true;
            }
        }
        //----------------------------------

        // Prepare others actions
        //----------------------------------
        if (!is_null($tempExistItem)) {

            do {

                if ($action == Ess_M2ePro_Model_Connector_Server_Ebay_Item_Dispatcher::ACTION_STOP) {
                    $this->deleteProduct($tempExistItem['instance']);
                    break;
                }

                if ($tempExistItem['action'] == Ess_M2ePro_Model_Connector_Server_Ebay_Item_Dispatcher::ACTION_STOP) {
                    return false;
                }

                if ($action == Ess_M2ePro_Model_Connector_Server_Ebay_Item_Dispatcher::ACTION_LIST) {
                    $this->deleteProduct($tempExistItem['instance']);
                    break;
                }

                if ($tempExistItem['action'] == Ess_M2ePro_Model_Connector_Server_Ebay_Item_Dispatcher::ACTION_LIST) {
                    return false;
                }

                if ($action == Ess_M2ePro_Model_Connector_Server_Ebay_Item_Dispatcher::ACTION_RELIST) {
                    $this->deleteProduct($tempExistItem['instance']);
                    break;
                }

                if ($tempExistItem['action'] == Ess_M2ePro_Model_Connector_Server_Ebay_Item_Dispatcher::ACTION_RELIST) {
                    return false;
                }

            } while (false);
        }
        //----------------------------------

        // Add new action for eBay
        //----------------------------------
        $this->_actionsProducts[$newListingsProductId] = array(
            'instance' => $listingProductInstance,
            'action' => $action,
            'params' => $params
        );
        //----------------------------------

        return true;
    }

    public function deleteProduct(Ess_M2ePro_Model_Listing_Product $listingProductInstance)
    {
        $listingProductId = $listingProductInstance->getId();

        if (isset($this->_actionsProducts[$listingProductId])) {
            unset($this->_actionsProducts[$listingProductId]);
            return true;
        }

        return false;
    }

    //-----------------------------------------

    public function isExistProductAction(Ess_M2ePro_Model_Listing_Product $listingProductInstance,
                                         $action, array $params = array())
    {
        $newListingsProductId = $listingProductInstance->getId();
        $params['status_changer'] = Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_SYNCH;

        if (!isset($this->_actionsProducts[$newListingsProductId])) {
            return false;
        }

        if ($this->_actionsProducts[$newListingsProductId]['action'] != $action) {
            return false;
        }

        $tempExistItem = $this->_actionsProducts[$newListingsProductId];

        foreach ($params as $tempParamKey => $tempParamValue) {

            if (!isset($tempExistItem['params'][$tempParamKey])) {
                return false;
            }

            if (is_array($tempExistItem['params'][$tempParamKey]) && is_array($tempParamValue)) {

                foreach ($tempParamValue as $tempParamKeyTwo => $tempParamValueTwo) {

                    if (!isset($tempExistItem['params'][$tempParamKey][$tempParamKeyTwo])) {
                        return false;
                    }
                    if ($tempExistItem['params'][$tempParamKey][$tempParamKeyTwo] != $tempParamValueTwo) {
                        return false;
                    }
                }

                continue;
            }

            if ($tempExistItem['params'][$tempParamKey] != $tempParamValue) {
                return false;
            }
        }

        return true;
    }

    public function removeAllProducts()
    {
        $this->_actionsProducts = array();
    }

    // ########################################

    public function execute(Ess_M2ePro_Model_Synchronization_LockItem $lockItem, $percentsFrom, $percentsTo)
    {
        $lockItem->activate();
        $lockItem->setPercents($percentsFrom);

        $lockItem->setStatus(Mage::helper('M2ePro')->__('Communication with eBay is started. Please wait...'));

        // Get prepared for actions array
        //----------------------------
        $actions = $this->makeActionsForExecute();
        //----------------------------

        // Calculate total count items
        //----------------------------
        $totalCount = 0;
        foreach ($actions as $combinations) {
            foreach ($combinations as $combination) {
                $totalCount += count($combination['items']);
            }
        }
        //----------------------------

        $results = array();

        if ($totalCount == 0) {
            $results[] = Ess_M2ePro_Model_Connector_Server_Ebay_Item_Abstract::STATUS_SUCCESS;
        } else {

            // Execute eBay actions
            //----------------------------
            $countProcessedItems = 0;
            $percentsOneProduct = ($percentsTo - $percentsFrom)/$totalCount;

            $waitMessage = Mage::helper('M2ePro')->__('product(s). Please wait...');

            foreach ($actions as $action=>$combinations) {
                foreach ($combinations as $combination) {

                    $tempCount = count($combination['items']);

                    $maxCountPerStep = 10;
                    $tempCount <= 25 && $maxCountPerStep = 5;
                    $tempCount <= 15 && $maxCountPerStep = 3;
                    $tempCount <= 8 && $maxCountPerStep = 2;
                    $tempCount <= 4 && $maxCountPerStep = 1;

                    for ($i=0; $i<count($combination['items']);$i+=$maxCountPerStep) {

                        $itemsForStep = array_slice($combination['items'],$i,$maxCountPerStep);
                        $countProcessedItems += count($itemsForStep);

                        // Set status for progress bar
                        //-----------------------------
                        $actionTitle = Ess_M2ePro_Model_Connector_Server_Ebay_Item_Dispatcher::getActionTitle($action);
                        $statusProductsIds = array();
                        foreach ($itemsForStep as $item) {
                            $statusProductsIds[] = $item->getData('product_id');
                        }
                        $lockItem->setStatus($actionTitle.' "'.implode('", "',$statusProductsIds).'" '.$waitMessage);
                        //-----------------------------

                        $tempResult = Mage::getModel('M2ePro/Connector_Server_Ebay_Item_Dispatcher')
                            ->process($action, $itemsForStep, $combination['params']);
                        $results  = array_merge($results,array($tempResult));

                        // Set percents for progress bar
                        //-----------------------------
                        $tempPercents = $percentsFrom + ($countProcessedItems * $percentsOneProduct);
                        $lockItem->setPercents($tempPercents > $percentsTo ? $percentsTo : $tempPercents);
                        //-----------------------------

                        $lockItem->activate();
                    }
                }
            }
            //----------------------------
        }

        $lockItem->setStatus(Mage::helper('M2ePro')->__('Communication with eBay is finished. Please wait...'));

        $lockItem->setPercents($percentsTo);
        $lockItem->activate();

        return Ess_M2ePro_Model_Connector_Server_Ebay_Item_Abstract::getMainStatus($results);
    }

    //-----------------------------------------

    private function makeActionsForExecute()
    {
        $actions = array(
            Ess_M2ePro_Model_Connector_Server_Ebay_Item_Dispatcher::ACTION_LIST => array(),
            Ess_M2ePro_Model_Connector_Server_Ebay_Item_Dispatcher::ACTION_RELIST => array(),
            Ess_M2ePro_Model_Connector_Server_Ebay_Item_Dispatcher::ACTION_REVISE => array(),
            Ess_M2ePro_Model_Connector_Server_Ebay_Item_Dispatcher::ACTION_STOP => array()
        );

        foreach ($this->_actionsProducts as $item) {

            // Get params hash
            //----------------------------
            $paramsHash = '';
            ksort($item['params']);

            foreach ($item['params'] as $keyParam => $valueParam) {

                if (is_array($valueParam)) {
                    ksort($valueParam);
                    foreach ($valueParam as $keyParamTwo => $valueParamTwo) {
                        $paramsHash .= (string)$keyParam.(string)$keyParamTwo.(string)$valueParamTwo;
                    }
                } else {
                    $paramsHash .= (string)$keyParam.(string)$valueParam;
                }
            }

            if ($paramsHash != '') {
                $paramsHash = md5($paramsHash);
            }
            //----------------------------

            // Add to output array
            //----------------------------
            $index = NULL;
            for ($i=0;$i<count($actions[$item['action']]);$i++) {
                $combination = $actions[$item['action']][$i];
                if ($combination['params_hash'] == $paramsHash) {
                    $index = $i;
                    break;
                }
            }
            if (is_null($index)) {
                $actions[$item['action']][] = array(
                    'items' => array($item['instance']),
                    'params' => $item['params'],
                    'params_hash' => $paramsHash
                );
            } else {
                $actions[$item['action']][$index]['items'][] = $item['instance'];
            }
            //----------------------------
        }

        return $actions;
    }

    // ########################################
}