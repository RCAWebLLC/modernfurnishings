<?php

class Bongo_Checkout_Block_Link extends Mage_Core_Block_Template {
	public function getIsEnabled() {
		//if (Mage::getStoreConfig ( 'bongocheckout_config/config/active' ) && (Mage::getStoreConfig ( 'bongocheckout_config/config/transfer_type' ) == "1" || Mage::getStoreConfig ( 'bongocheckout_config/config/transfer_type' ) == "3") && Mage::getModel ( 'bongocheckout/shipping_carrier_bongo' )->checkSkuStatus ( Mage::getSingleton ( 'checkout/session' )->getQuote ()->getAllItems (), false )) {
		if (Mage::getStoreConfig ( 'bongocheckout_config/config/active' ) && (Mage::getStoreConfig ( 'bongocheckout_config/config/transfer_type' ) == "1" || Mage::getStoreConfig ( 'bongocheckout_config/config/transfer_type' ) == "3")) {
			return true;
		} else {
			return false;
		}
	}
}