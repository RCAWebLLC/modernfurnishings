<?php

class Bongo_Checkout_Model_Payment_Standard extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'bongocheckout';
	
	protected $_canUseInternal = true;
	protected $_canUseCheckout = false;
	protected $_canUseForMultishipping = false;
	protected $_canManageRecurringProfiles = false;
	
	public function authorize(Varien_Object $payment, $amount) {
		// nothing to do, internal module used for tracking only
	}
	
	public function capture(Varien_Object $payment, $amount) {
		// nothing to do, internal module used for tracking only
	}
}

?>