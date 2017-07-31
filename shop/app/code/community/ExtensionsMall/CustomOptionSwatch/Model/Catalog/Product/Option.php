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
class ExtensionsMall_CustomOptionSwatch_Model_Catalog_Product_Option extends Mage_Catalog_Model_Product_Option {

    const OPTION_GROUP_SWATCH = 'swatches';
    const OPTION_TYPE_SWATCH = 'swatch';

    public function getGroupByType($type = null) {
        if (is_null($type)) {
            $type = $this->getType();
        }

        if ($type == self::OPTION_TYPE_SWATCH) {
            return self::OPTION_GROUP_SWATCH;
        } else {
            return parent::getGroupByType($type);
        }
    }

    protected function _afterDelete() {

        Mage::getResourceModel('custom_option_swatch/swatches_relation')->removeSwatchByOptionId($this->getId());

        return parent::_afterDelete();
    }

}
