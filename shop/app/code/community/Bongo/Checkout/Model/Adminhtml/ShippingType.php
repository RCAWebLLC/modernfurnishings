<?php
class Bongo_Checkout_Model_Adminhtml_ShippingType {
	public function toOptionArray() {
		$activeCarriers = Mage::getSingleton ( 'shipping/config' )->getActiveCarriers ();
		$methods = array ();
		$options = array (array ('value' => 'bongocheckout_free', 'label' => 'Free' ), array ('value' => 'bongocheckout_manual', 'label' => 'Per Product' ) );
		$methods [] = array ('value' => $options, 'label' => 'Bongo' );
		
		foreach ( $activeCarriers as $carrierCode => $carrierModel ) {
			if (empty ( $carrierCode ) || $carrierCode == 'bongocheckout') {
				continue;
			}
			
			if ($carrierMethods = $carrierModel->getAllowedMethods ()) {
				$options = array ();
				
				foreach ( $carrierMethods as $methodCode => $method ) {
					$options [] = array ('value' => "{$carrierCode}_{$methodCode}", 'label' => $method );
				}
				
				$carrierTitle = Mage::getStoreConfig ( "carriers/{$carrierCode}/title" );
				
				$methods [] = array ('value' => $options, 'label' => $carrierTitle );
			}
		}
		
		return $methods;
	}

}