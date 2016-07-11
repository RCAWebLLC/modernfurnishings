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
class ExtensionsMall_CustomOptionSwatch_Block_Adminhtml_Swatches_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('swatchesGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('custom_option_swatch/swatches')->getCollection();
        $this->setCollection($collection);

        $myCollection = parent::_prepareCollection();

        foreach ($myCollection->getCollection() as $preview) {
            $preview->setData('swatch_preview', "<img title=\"{$preview->getData('title')}\" alt=\"{$preview->getData('name')}\" src=\"" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "custom_option_swatch/{$preview->getId()}/" . $preview->getData('image_base') . "\" height=\"30px\" width=\"30\" />");
        }

        return $myCollection;
    }

    protected function _prepareColumns() {

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('custom_option_swatch')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
        ));

        $this->addColumn('swatch_preview', array(
            'header' => Mage::helper('custom_option_swatch')->__('Swatch preview'),
            'type' => 'text',
            'align' => 'center',
            'width' => '50px',
            'index' => 'swatch_preview',
            'sortable' => false,
            'filter' => false
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('custom_option_swatch')->__('Label'),
            'align' => 'left',
            'index' => 'name'
        ));

	$this->addColumn('description', array(
            'header' => Mage::helper('custom_option_swatch')->__('Description'),
            'align' => 'left',
            'index' => 'description'
        ));

        $this->addColumn('image_base', array(
            'header' => Mage::helper('custom_option_swatch')->__('File'),
            'align' => 'left',
            'index' => 'image_base'
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('custom_option_swatch')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('custom_option_swatch')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
