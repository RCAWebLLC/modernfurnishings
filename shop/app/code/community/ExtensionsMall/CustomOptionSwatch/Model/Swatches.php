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
class ExtensionsMall_CustomOptionSwatch_Model_Swatches extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('custom_option_swatch/swatches');
    }
    
    public function createFileName($name) {
        $lowerCase = strtolower($name);
        $replaced = preg_replace('/[^a-zA-Z0-9-]/', '-', $lowerCase);
        return $replaced;
    }

    protected function _beforeDelete() {
        parent::_beforeDelete();
        if (Mage::getResourceModel('custom_option_swatch/swatches_relation')->isUsed($this->getId())) {
            throw new Exception(Mage::helper('custom_option_swatch')->__("Swatch is currently in use and cannot be deleted. Please remove all option related to this swatch and then try to delete it."));
        }
        return $this;
    }

}
