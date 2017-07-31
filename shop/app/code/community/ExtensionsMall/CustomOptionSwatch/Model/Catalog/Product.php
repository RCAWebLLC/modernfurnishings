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
class ExtensionsMall_CustomOptionSwatch_Model_Catalog_Product extends Mage_Catalog_Model_Product {

    /**
     * Create duplicate
     *
     * @return Mage_Catalog_Model_Product
     */
    public function duplicate() {
        $newProduct = parent::duplicate();
        $newProductId = $newProduct->getId();

        $this->duplicateSwatches($newProductId);

        return $newProduct;
    }

    protected function duplicateSwatches($newProductId) {
        $observer = Mage::getSingleton('ExtensionsMall_CustomOptionSwatch_Model_Observer');
        $observerSwatches = $observer->getSwatches();
        $product = Mage::getModel('catalog/product')->load($newProductId);
        $options = $product->getOptions();
        $swatches = array();
        $group = 0;
        foreach ($options as $o) {
            $optionType = $o->getType();
            if ($optionType == 'swatch') {
                $values = $o->getValues();
                $i = 0;
                foreach ($values as $option_type_id => $v) {
                    $swatches[$group][$i]['swatch_id'] = $observerSwatches[$group][$i]['swatch_id'];
                    $swatches[$group][$i]['product_id'] = $newProductId;
                    $swatches[$group][$i]['option_id'] = $v->getData('option_id');
                    $swatches[$group][$i]['option_type_id'] = $v->getData('option_type_id');
                    $i++;
                }
            }
            $group++;
        }
        $this->saveSwatches($swatches);
    }

    protected function saveSwatches($swatches) {
        foreach ($swatches as $group) {
            foreach ($group as $data) {
                $swatchRelation = Mage::getResourceModel('custom_option_swatch/swatches_relation');
                $swatchRelation->assignItem($data);
            }
        }
    }

}
