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
class ExtensionsMall_CustomOptionSwatch_Model_Catalog_Product_Option_Value extends Mage_Catalog_Model_Product_Option_Value {

    protected $_eventPrefix = 'product_option_value';

    protected function _afterSave() {

        Mage::getResourceModel('custom_option_swatch/swatches_relation')->assignItem(array(
            'swatch_id' => new Zend_Db_Expr($this->getSwatchId()),
            'product_id' => new Zend_Db_Expr($this->getProduct()->getId()),
            'option_id' => new Zend_Db_Expr($this->getOption()->getId()),
            'option_type_id' => new Zend_Db_Expr($this->getOptionTypeId()),
                )
        );

        return parent::_afterSave();
    }

    protected function _afterDelete() {
        try {
            Mage::getResourceModel('custom_option_swatch/swatches_relation')->removeSwatchByOptionTypeId($this->getSwatchId(), $this->getOptionTypeId());
        } catch (Exception $e) {
            
        }
        return parent::_afterDelete();
    }

    public function getValuesByOption($optionIds, $option_id, $store_id) {
        $collection = Mage::getResourceModel('catalog/product_option_value_collection')
                ->addFieldToFilter('main_table.option_id', $option_id)
                ->getValuesByOption($optionIds, $store_id);
        return $collection;
    }

}
