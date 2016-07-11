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
class ExtensionsMall_CustomOptionSwatch_Block_Adminhtml_Catalog_Product_Edit_Tab_Options_Type_Swatch extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('customoptionswatch/catalog/product/edit/options/type/swatch.phtml');
    }

    protected function _prepareLayout() {

        $this->setChild('add_swatch_row_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('catalog')->__('Select Swatches'),
                            'class' => 'add add-swatch-row',
                            'id' => 'add_swatch_row_button_{{option_id}}',
                        ))
        );

        $this->setChild('delete_swatch_row_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('catalog')->__('Delete Row'),
                            'class' => 'delete delete-swatch-row icon-btn',
                        ))
        );

        return parent::_prepareLayout();
    }

    public function getSwatchSelectBox() {
        return $this->getChildHtml('add_swatch_row_swatch_select');
    }

    public function getAddButtonHtml() {
        return $this->getChildHtml('add_swatch_row_button');
    }

    public function getDeleteButtonHtml() {
        return $this->getChildHtml('delete_swatch_row_button');
    }

    public function getPriceTypeSelectHtml() {
        $this->getChild('option_price_type')
                ->setData('id', 'product_option_{{id}}_swatch_{{select_id}}_price_type')
                ->setName('product[options][{{id}}][values][{{select_id}}][price_type]');
        return parent::getPriceTypeSelectHtml();
    }

}
