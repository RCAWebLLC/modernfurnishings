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
class ExtensionsMall_CustomOptionSwatch_Block_Adminhtml_Custom_Option_Swatch_Selection_Search extends Mage_Adminhtml_Block_Widget {

    protected function _construct() {
        $this->setId('custom_option_swatch_selection_search');
        $this->setTemplate('customoptionswatch/catalog/product/edit/options/selection/search.phtml');
    }

    public function getHeaderText() {
        return Mage::helper('custom_option_swatch')->__('Please Select Swatch to Add');
    }

    protected function _prepareLayout() {
        $this->setChild(
                'grid', $this->getLayout()->createBlock('custom_option_swatch/adminhtml_custom_option_swatch_selection_search_grid', 'adminhtml.catalog.product.edit.tab.bundle.option.search.grid')
        );
        return parent::_prepareLayout();
    }

    protected function _beforeToHtml() {
        $this->getChild('grid')->setIndex($this->getIndex())
                ->setFirstShow($this->getFirstShow());

        return parent::_beforeToHtml();
    }

    public function getButtonsHtml() {
        $addButtonData = array(
            'id' => 'add_button_' . $this->getIndex(),
            'label' => Mage::helper('custom_option_swatch')->__('Add Selected Swatches to Option'),
            'onclick' => 'swatchOptionType.swatchGridAddSelected(event)',
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }

    public function getHeaderCssClass() {
        return 'head-catalog-product-custom-option-swatch-selction';
    }

}
