<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_ProductChange extends Ess_M2ePro_Model_Abstract
{
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    const CREATOR_TYPE_OBSERVER = 1;
    const CREATOR_TYPE_SYNCHRONIZATION = 2;

    //####################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/ProductChange');
    }

    //####################################

    public function addCreateAction($productId, $creatorType = self::CREATOR_TYPE_OBSERVER)
    {
         return $this->setOnlyAction($productId, self::ACTION_CREATE, $creatorType);
    }

    public function addDeleteAction($productId, $creatorType = self::CREATOR_TYPE_OBSERVER)
    {
         return $this->setOnlyAction($productId, self::ACTION_DELETE, $creatorType);
    }

    public function updateAttribute($productId,
                                    $attribute,
                                    $valueOld,
                                    $valueNew,
                                    $creatorType = self::CREATOR_TYPE_OBSERVER)
    {
         $tempChanges = Mage::getModel('M2ePro/ProductChange')
                                ->getCollection()
                                ->addFieldToFilter('product_id', $productId)
                                ->addFieldToFilter('action', self::ACTION_UPDATE)
                                ->addFieldToFilter('attribute', $attribute)
                                ->toArray();

         if ($tempChanges['totalRecords'] <= 0) {

             if ($valueOld == $valueNew) {
                 return false;
             }

             $dataForAdd = array( 'product_id' => $productId,
                                  'action' => self::ACTION_UPDATE,
                                  'attribute' => $attribute,
                                  'value_old' => $valueOld,
                                  'value_new' => $valueNew,
                                  'count_changes' => 1,
                                  'creator_type' => $creatorType );

             Mage::getModel('M2ePro/ProductChange')
                     ->setData($dataForAdd)
                     ->save()
                     ->getId();

             return true;
         }

         if ($tempChanges['items'][0]['value_old'] == $valueNew) {

              Mage::getModel('M2ePro/ProductChange')
                    ->setId($tempChanges['items'][0]['id'])
                    ->delete();

              return true;

         } else if ($valueOld != $valueNew) {

             $dataForUpdate = array( 'value_new' => $valueNew,
                                     'count_changes' => $tempChanges['items'][0]['count_changes']+1,
                                     'creator_type' => $creatorType );

             Mage::getModel('M2ePro/ProductChange')
                     ->load($tempChanges['items'][0]['id'])
                     ->addData($dataForUpdate)
                     ->save()
                     ->getId();

             return true;
         }

         return false;
    }

    //-----------------------------------

    public function clearAll($creatorType = self::CREATOR_TYPE_OBSERVER, $maximumDate = NULL)
    {
        /** @var $connWrite Varien_Db_Adapter_Pdo_Mysql */
        $connWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tableName = $this->getResource()->getMainTable();

        $where = array('creator_type = ?'=>$creatorType);
        !is_null($maximumDate) && $where['update_date <= ?'] = $maximumDate;

        $connWrite->delete($tableName,$where);
    }

    //####################################

    private function setOnlyAction($productId , $action, $creatorType = self::CREATOR_TYPE_OBSERVER)
    {
        /** @var $connWrite Varien_Db_Adapter_Pdo_Mysql */
        $connWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connWrite->delete($this->getResource()->getMainTable(),array('product_id = ?'=>(int)$productId));

        $dataForAdd = array('product_id' => $productId,
                            'action' => $action,
                            'creator_type' => $creatorType );

        Mage::getModel('M2ePro/ProductChange')
                 ->setData($dataForAdd)
                 ->save()
                 ->getId();

        return true;
    }

    //####################################
}