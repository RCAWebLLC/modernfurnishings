<?php

class Bongo_Checkout_Block_Adminhtml_Sales_Order_View_Bongocheckout extends Mage_Adminhtml_Block_Sales_Order_Abstract {
	public function getIsEnabled() {
		if (Mage::getStoreConfig ( 'bongocheckout_config/config/active' )) {
			return true;
		} else {
			return false;
		}
	}
}