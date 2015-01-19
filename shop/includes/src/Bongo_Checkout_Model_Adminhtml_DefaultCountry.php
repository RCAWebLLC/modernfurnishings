<?php

class Bongo_Checkout_Model_Adminhtml_DefaultCountry {
	public function getCommentText() {
		$setup = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');
		
		if ($setup->getAttributeId ( 'catalog_product', 'country_of_manufacture' ) === false) {
			return "Bongo Checkout requires a Country of Manufacture for every product.  Your version of Magento does not have the Country of Manufacture attribute on products so the default value you set here will be used for ALL products.<br /><b>If you'd like to specify a Country of Manufacture on a per-product basis, please upgrade to the latest version of Magento.</b>";
		} else {
			return "Bongo Checkout requires a Country of Manufacture for every product.  If you do not specify a Country of Manufacture for certain products, the default value you set here will be used.";
		}
	}
}