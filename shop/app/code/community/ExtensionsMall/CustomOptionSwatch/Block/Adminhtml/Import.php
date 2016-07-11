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
class ExtensionsMall_CustomOptionSwatch_Block_Adminhtml_Import extends Mage_Adminhtml_Block_Template {

    protected function _construct() {
        
        $this->_blockGroup = 'sizechart';
        $this->_controller = 'adminhtml_import';
        $this->_headerText = Mage::helper('custom_option_swatch')->__('Custom Option Swatch Logs');
        parent::_construct();

     /*   $this->_objectId = 'import_id';
        $this->_blockGroup = 'custom_option_swatch';
        $this->_controller = 'custom_option_swatch/adminhtml_import'; */
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText() {
        return Mage::helper('custom_option_swatch')->__('Import Swatches');
    }

}
