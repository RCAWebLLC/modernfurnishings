<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Mysql4_Amazon_Order extends Ess_M2ePro_Model_Mysql4_Component_Child_Abstract
{
    protected $_isPkAutoIncrement = false;

    // ########################################

    public function _construct()
    {
        $this->_init('M2ePro/Amazon_Order', 'order_id');
        $this->_isPkAutoIncrement = false;
    }

    // ########################################

    public function getItemsTotal($orderId)
    {
        /** @var $collection Ess_M2ePro_Model_Mysql4_Amazon_Order_Collection */
        $collection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Order_Item');
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $collection->addFieldToFilter('order_id', (int)$orderId)->addExpressionFieldToSelect(
            'items_total', new Zend_Db_Expr('SUM(`price`*`qty_purchased`)'), 'price'
        );
        $collection->getSelect()->group('order_id');

        return round($collection->getFirstItem()->getData('items_total'), 2);
    }

    // ########################################
}