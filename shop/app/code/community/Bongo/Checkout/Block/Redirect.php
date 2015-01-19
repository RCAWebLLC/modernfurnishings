<?php

class Bongo_Checkout_Block_Redirect extends Mage_Core_Block_Template {
	public function getIsEnabled() {
		$allowed_countries = explode ( ',', Mage::getStoreConfig ( 'bongocheckout_config/config/allow_countries' ) );
		
		if (Mage::getStoreConfig ( 'bongocheckout_config/config/active' ) && (Mage::getStoreConfig ( 'bongocheckout_config/config/transfer_type' ) == "2" || Mage::getStoreConfig ( 'bongocheckout_config/config/transfer_type' ) == "3") && in_array ( Mage::getSingleton ( 'checkout/session' )->getQuote ()->getShippingAddress ()->getCountryId (), $allowed_countries )) {
			/*if (! Mage::getModel ( 'bongocheckout/shipping_carrier_bongo' )->checkSkuStatus ( Mage::getSingleton ( 'checkout/session' )->getQuote ()->getAllItems (), true )) {
				session_write_close ();
				$this->_redirect ( 'checkout/cart' );
			}*/
			
			return true;
		} else {
			return false;
		}
	}
	
	public function getCheckoutUrl() {
		return Mage::getStoreConfig ( 'bongocheckout_config/config/checkout_url' );
	}
}