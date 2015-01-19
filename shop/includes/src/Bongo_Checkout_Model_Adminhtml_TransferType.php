<?php
class Bongo_Checkout_Model_Adminhtml_TransferType {
	public function toOptionArray() {
		return array (array ('value' => 1, 'label' => Mage::helper ( 'bongocheckout' )->__ ( 'International Checkout Button' ) ), array ('value' => 2, 'label' => Mage::helper ( 'bongocheckout' )->__ ( 'Auto Redirect' ) ), array ('value' => 3, 'label' => Mage::helper ( 'bongocheckout' )->__ ( 'International Checkout Button & Auto Redirect' ) ) );
	}

}