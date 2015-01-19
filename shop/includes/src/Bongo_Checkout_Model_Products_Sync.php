<?php

class Bongo_Checkout_Model_Products_Sync {
	public function sync($send = false) {
		$products = Mage::getModel ( 'catalog/product' )->getCollection ();
		$items = array ();
		$country_manufacture = Mage::getStoreConfig ( 'bongocheckout_config/config/country_manufacture' );
		
		foreach ( $products as $prod ) {
			$product = Mage::getModel ( 'catalog/product' )->load ( $prod->getId () );
			
			if ($product->getTypeId () == 'configurable' || $product->getTypeId () == 'bundle' || $product->getTypeId () == 'grouped') {
				continue;
			}
			
			$price = $product->getSpecialPrice ();
			
			if ($price < 0.01) {
				$price = $product->getPrice ();
			}
			
			$product_url = '';
			$image_url = '';
			
			try {
				$product_url = $product->getProductUrl ();
				$image_url = $product->getImageUrl ();
			} catch ( Exception $e ) {
			}
			
			$items [] = array ('productID' => $product->getSku (), 'description' => $product->getShortDescription (), 'url' => $product_url, 'imageUrl' => $image_url, 'price' => $price, 'countryOfOrigin' => $product->getCountryOfManufacture () ? $product->getCountryOfManufacture () : $country_manufacture, 'hsCode' => '', 'eccn' => '', 'hazFlag' => '', 'licenseFlag' => '', 'importFlag' => '', 'productType' => '', 'itemInformation' => array (array ('l' => '', 'w' => '', 'h' => '', 'wt' => $product->getWeight () ) ) );
		}
		
		if ($send) {
			$client = new SoapClient ( "https://api.bongous.com/services/v4?wsdl" );
			$params = ( object ) array ('partnerKey' => Mage::getStoreConfig ( 'bongocheckout_config/config/api_key' ), 'language' => 'en', 'items' => $items );
			return $client->connectProductInfo ( $params );
		} else {
			return $items;
		}
	}
	
	public function cron() {
		$frequency = Mage::getStoreConfig ( 'bongocheckout_products/automated/frequency' );
		
		if ($frequency == "1" || ($frequency == "2" && date ( 'w' ) == "0") || ($frequency == "3" && date ( 'j' ) == "1")) {
			$response = $this->sync ( true );
			Mage::log ( "Cron synced on " . date ( "Y-m-d H:i:s", Mage::getModel ( 'core/date' )->timestamp ( time () ) ) . "; Raw Response: " . print_r ( $response, true ), null, 'bongo_cron.log' );
		}
		
		return true;
	}
}