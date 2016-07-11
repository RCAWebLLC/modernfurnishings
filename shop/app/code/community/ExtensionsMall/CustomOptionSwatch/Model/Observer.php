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
class ExtensionsMall_CustomOptionSwatch_Model_Observer {

    protected $swatches = array();

    public function duplicate($observer) {
        $product = $observer->getEvent()->getCurrentProduct();
        $options = $product->getOptions();
        $group = 0;
        foreach ($options as $o) {
            $optionType = $o->getType();
            if ($optionType == 'swatch') {
                $values = $o->getValues();
                foreach ($values as $option_type_id => $v) {
                    $this->swatches[$group][] = $v->getData();
                }
            }
            $group++;
        }
    }

    public function getSwatches() {
        return $this->swatches;
    }

}
