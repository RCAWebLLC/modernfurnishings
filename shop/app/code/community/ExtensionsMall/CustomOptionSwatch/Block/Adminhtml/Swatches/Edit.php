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
class ExtensionsMall_CustomOptionSwatch_Block_Adminhtml_Swatches_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'custom_option_swatch';
        $this->_controller = 'adminhtml_swatches';

        $this->_updateButton('save', 'label', Mage::helper('custom_option_swatch')->__('Save Swatch'));
        $this->_updateButton('delete', 'label', Mage::helper('custom_option_swatch')->__('Delete Swatch'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('press_content') == null) {
					tinyMCE.execCommand('mceAddControl', false, 'custom_option_swatch_content');
				} else {
					tinyMCE.execCommand('mceRemoveControl', false, 'custom_option_swatch_content');
				}
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
    }

    public function getHeaderText() {
        if (Mage::registry('custom_option_swatch') && Mage::registry('custom_option_swatch')->getId()) {
            return Mage::helper('custom_option_swatch')->__("Edit Swatch '%s'", $this->htmlEscape(Mage::registry('custom_option_swatch')->getName()));
        } else {
            return Mage::helper('custom_option_swatch')->__('Add Swatch');
        }
    }

}
