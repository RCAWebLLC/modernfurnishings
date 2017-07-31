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
class ExtensionsMall_CustomOptionSwatch_Block_Adminhtml_Custom_Option_Swatch_Selection_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('custom_option_swatch_selection_search_grid');
        $this->setRowClickCallback('swatchOptionType.swatchGridRowClick.bind(swatchOptionType)');
        $this->setCheckboxCheckCallback('swatchOptionType.swatchGridCheckboxCheck.bind(swatchOptionType)');
        $this->setRowInitCallback('swatchOptionType.swatchGridRowInit.bind(swatchOptionType)');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    protected function _beforeToHtml() {
        $this->setId($this->getId() . '_' . $this->getIndex());
        $this->getChild('reset_filter_button')->setData('onclick', $this->getJsObjectName() . '.resetFilter()');
        $this->getChild('search_button')->setData('onclick', $this->getJsObjectName() . '.doFilter()');

        return parent::_beforeToHtml();
    }

    protected function _prepareCollection() {

        $collection = Mage::getModel('custom_option_swatch/swatches')->getCollection();
        if ($this->_getSwatches()) {
            $collection->getSelect()->where('entity_id NOT IN (?)', $this->_getSwatches());
        }

        $this->setCollection($collection);
        $myCollection = parent::_prepareCollection();

        foreach ($myCollection->getCollection() as $preview) {
            $preview->setData('swatch_preview', "<img title=\"{$preview->getData('title')}\" alt=\"{$preview->getData('name')}\" src=\"" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "custom_option_swatch/{$preview->getId()}/" . $preview->getData('image_base') . "\" height=\"30px\" width=\"30\" />");
        }

        return $myCollection;
    }

    protected function _prepareColumns() {

        $this->addColumn('id', array(
            'header' => Mage::helper('custom_option_swatch')->__('ID'),
            'sortable' => true,
            'width' => '60px',
            'index' => 'entity_id'
        ));

        $this->addColumn('swatch_preview', array(
            'header' => Mage::helper('custom_option_swatch')->__('Swatch preview'),
            'type' => 'text',
            'align' => 'center',
            'width' => '50px',
            'index' => 'swatch_preview',
            'sortable' => false,
            'filter' => false,
            'column_css_class' => 'swatch_preview'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('custom_option_swatch')->__('Swatch Title'),
            'index' => 'name',
            'column_css_class' => 'name'
        ));

        $this->addColumn('is_selected', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_selected',
            'align' => 'center',
            'filter' => false,
            'values' => $this->_getSelectedSwatches(),
            'index' => 'entity_id',
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/adminhtml_swatch_selection/grid', array('index' => $this->getIndex(), 'swatches' => implode(',', $this->_getSwatches())));
    }

    protected function _getSelectedSwatches() {
        $swatches = $this->getRequest()->getPost('swatches', array());
        return $swatches;
    }

    protected function _getSwatches() {
        if ($swatches = $this->getRequest()->getPost('swatches', null)) {
            return $swatches;
        } else if ($swatches = $this->getRequest()->getParam('swatches', null)) {
            return explode(',', $swatches);
        } else {
            return array();
        }
    }

    public function getStore() {
        return Mage::app()->getStore();
    }

}
