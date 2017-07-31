<?php

/**
 * ExtensionsMall
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ExtensionsMall EULA that is available through 
 * the world-wide-web at this URL: http://www.extensionsmall.com/license.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to support@extensionsmall.com for more information.
 *
 * @category   ExtensionsMall
 * @package    ExtensionsMall_CustomOptionSwatch
 * @author     ExtensionsMall Dev Team
 * @copyright  Copyright (c) 2015 ExtensionsMall (http://www.extensionsmall.com/)
 * @license    http://www.extensionsmall.com/license.html
 */
class ExtensionsMall_CustomOptionSwatch_Model_Resource_Swatches_Relation extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('custom_option_swatch/custom_option_swatch_relation', 'swatch_id');
    }

    function assignItem($data) {
        try {
            $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
        } catch (Exception $e) {
            // To Do
        }
    }

    function isUsed($swatchId) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->where('swatch_id = ?', $swatchId);
        return $this->_getReadAdapter()->fetchRow($select) ? true : false;
    }

    function getSwatchByOptionoTypeId($value) {
        $select = $this->_getReadAdapter()->select()
                ->from(array('main_table' => $this->getMainTable()))
                ->join(array('swatch' => $this->getTable('custom_option_swatch_images')), 'swatch.entity_id=main_table.swatch_id', array('image_base'))
                ->where('main_table.option_type_id = ?', $value);
        return new Varien_Object($this->_getReadAdapter()->fetchRow($select));
    }

    function removeSwatchByOptionTypeId($swatchId, $optionTypeId) {

        try {
            $condition = array(
                'swatch_id = ?' => (int) $swatchId,
                'option_type_id = ?' => (int) $optionTypeId,
            );
            $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        } catch (Exception $e) {
            // To Do
        }
        return $this;
    }

    function removeSwatchByOptionId($optionId) {

        try {
            $condition = array(
                'option_id = ?' => (int) $optionId,
            );
            $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        } catch (Exception $e) {
            // To Do		
        }
        return $this;
    }

}
