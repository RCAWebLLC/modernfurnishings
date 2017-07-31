<?php

class ExtensionsMall_CustomOptionSwatch_Block_Adminhtml_Import_Log extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_blockGroup = 'custom_option_swatch';
        $this->_controller = 'adminhtml_import_log';
        $this->_headerText = Mage::helper('custom_option_swatch')->__('Custom Option Swatch Import');
        $this->_addButtonLabel = Mage::helper('custom_option_swatch')->__('New Import');
        parent::__construct();
    }

}
