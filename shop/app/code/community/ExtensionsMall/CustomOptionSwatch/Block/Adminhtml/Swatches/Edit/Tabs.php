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
class ExtensionsMall_CustomOptionSwatch_Block_Adminhtml_Swatches_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('swatches_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('custom_option_swatch')->__('Swatch Information'));
    }

    protected function _beforeToHtml() {

        $this->addTab('form_section', array(
            'label' => Mage::helper('custom_option_swatch')->__('Swatch Information'),
            'title' => Mage::helper('custom_option_swatch')->__('Swatch Information'),
            'content' => $this->getLayout()->createBlock('custom_option_swatch/adminhtml_swatches_edit_tab_form')->toHtml()
        ));

        return parent::_beforeToHtml();
    }

}
