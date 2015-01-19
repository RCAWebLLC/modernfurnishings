<?php

class Bongo_Checkout_Model_Products_Status {
	public function status($skus, $names, $error = false) {
		$client = new SoapClient ( "https://api.bongous.com/services/v4?wsdl" );
		$params = ( object ) array ('partnerKey' => Mage::getStoreConfig ( 'bongocheckout_config/config/api_key' ), 'language' => 'en', 'items' => $skus );
		$response = $client->connectSkuStatus ( $params );
		
		if ($response->error > 0) {
			return false;
		}
		
		$items = $response->items;
		$failed = array ();
		
		foreach ( $items as $item ) {
			if (( int ) $item->productStatus !== 2) {
				$failed [] = $names [$item->productID];
			}
		}
		
		if (count ( $failed ) > 0) {
			if ($error) {
				Mage::getSingleton ( 'checkout/session' )->addError ( Mage::helper ( 'bongocheckout' )->__ ( 'The following products cannot be shipped internationally at this time.  Please remove these products from your shopping cart before proceeding.' ) . '<br /><ul style="list-style:disc outside none !important;margin-left:20px !important;"><li>' . implode ( '</li><li>', $failed ) . '</li></ul>' );
			}
			
			return false;
		}
		
		return true;
	}
}