<?php

class Bongo_Checkout_Model_Shipping_Carrier_Bongo extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {
	protected $_code = 'bongocheckout';
	
	public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
		if (! $this->getConfigFlag ( 'active' ) || ! Mage::getStoreConfig ( 'bongocheckout_config/config/active' )) {
			return false;
		}
		
		$allowed_countries = explode ( ',', Mage::getStoreConfig ( 'bongocheckout_config/config/allow_countries' ) );
		
		if (! in_array ( $request->getDestCountryId (), $allowed_countries )) {
			return false;
		}
		
		if ($request->getDestCountryId () == Mage::getStoreConfig ( 'bongocheckout_config/dc/country' ) && $request->getDestRegionId () == Mage::getStoreConfig ( 'bongocheckout_config/dc/state' ) && $request->getDestCity () == Mage::getStoreConfig ( 'bongocheckout_config/dc/city' ) && $request->getDestPostcode () == Mage::getStoreConfig ( 'bongocheckout_config/dc/zip' ) && $request->getDestStreet ( 0 ) == Mage::getStoreConfig ( 'bongocheckout_config/dc/address1' ) && $request->getDestStreet ( 1 ) == Mage::getStoreConfig ( 'bongocheckout_config/dc/address2' )) {
			return false;
		}
		
		//$this->checkSkuStatus ( $request->getAllItems (), true );
		

		$items = array ();
		$names = array ();
		$domestic_shipping = $this->getDomesticShipping ( $request->getAllItems () );
		
		if ($request->getAllItems ()) {
			foreach ( $request->getAllItems () as $item ) {
				if (! $this->isSimpleProduct ( $item->getProduct () )) {
					continue;
				}
				
				$items [] = array ('productID' => $item->getSku (), 'quantity' => $item->getQty (), 'price' => $item->getProduct ()->getFinalPrice () );
				$names [$item->getSku ()] = $item->getName ();
			}
		}
		
		$client = new SoapClient ( "https://api.bongous.com/services/v4?wsdl" );
		$params = array ('partnerKey' => Mage::getStoreConfig ( 'bongocheckout_config/config/api_key' ), 'language' => 'en', 'privateIndividuals' => 'Y', 'shipmentOriginCountry' => 'US', 'shipmentDestinationCountry' => $request->getDestCountryId (), 'domesticShippingCost' => $domestic_shipping, 'items' => $items, 'insuranceFlag' => '', 'currency' => '', 'currencyConversionRate' => '' );
		$response_economy = $client->ConnectLandedCost ( ( object ) ($params + array ('service' => '1' )) );
		
		if (! preg_match ( '/checkout\/cart\/[add|delete|updatePost]/i', $_SERVER ['REQUEST_URI'] ) && (in_array ( ( int ) $response_economy->error, array (2025, 2026, 2027, 2028, 2029, 2030, 2031 ) ) || preg_match ( '/[2025|2026|2027|2028|2029|2030|2031]/', ( string ) $response_economy->errorMessageDetail ))) {
			$failed = array ();
			
			foreach ( $response_economy->items as $item ) {
				if (( int ) $item->calculated == 0) {
					$failed [] = $names [$item->productID];
				}
			}
			
			$has_error = false;
			$messages = Mage::getSingleton ( 'checkout/session' )->getMessages ()->getItems ();
			
			foreach ( $messages as $message ) {
				if (stripos ( $message->getText (), Mage::helper ( 'bongocheckout' )->__ ( 'The following products cannot be shipped internationally at this time.  Please remove these products from your shopping cart before proceeding.' ) ) !== 'false') {
					$has_error = true;
				}
			}
			
			if (count ( $failed ) > 0 && ! $has_error) {
				Mage::getSingleton ( 'checkout/session' )->addError ( Mage::helper ( 'bongocheckout' )->__ ( 'The following products cannot be shipped internationally at this time.  Please remove these products from your shopping cart before proceeding.' ) . '<br /><ul style="list-style:disc outside none !important;margin-left:20px !important;"><li>' . implode ( '</li><li>', $failed ) . '</li></ul>' );
			}
		}
		
		//$shipping_cost = $response_economy->shippingCost + $response_economy->dutyCost + $response_economy->taxCost;
		$shipping_cost = $response_economy->shippingCost;
		
		$result = Mage::getModel ( 'shipping/rate_result' );
		
		$method = Mage::getModel ( 'shipping/rate_result_method' );
		$method->setCarrier ( 'bongocheckout' );
		$method->setCarrierTitle ( $this->getConfigData ( 'title' ) );
		$method->setMethod ( 'economy' );
		$method->setMethodTitle ( $this->getConfigData ( 'name' ) );
		$method->setPrice ( $shipping_cost );
		$method->setCost ( $shipping_cost );
		$result->append ( $method );
		
		return $result;
	}
	
	public function checkSkuStatus($items, $error = false) {
		if ($items) {
			$skus = array ();
			$names = array ();
			
			foreach ( $items as $item ) {
				if (! $this->isSimpleProduct ( $item->getProduct () )) {
					continue;
				}
				
				$skus [] = array ('productID' => $item->getSku () );
				$names [$item->getSku ()] = $item->getName ();
			}
			
			if (! Mage::getModel ( 'bongocheckout/products_status' )->status ( $skus, $names, $error )) {
				return false;
			}
		}
		
		return true;
	}
	
	public function getDomesticShipping($items) {
		$domestic_shipping = 0;
		$domestic_method = Mage::getStoreConfig ( 'bongocheckout_config/config/shipping_type' );
		$domestic_default = Mage::getStoreConfig ( 'bongocheckout_config/config/domestic_shipping' );
		
		if ($domestic_method == 'bongocheckout_manual') {
			if ($items) {
				foreach ( $items as $item ) {
					if (! $this->isSimpleProduct ( $item->getProduct () )) {
						continue;
					}
					
					$_product = Mage::getModel ( 'catalog/product' )->load ( $item->getProduct ()->getId () );
					$domestic_shipping += ($_product->getBongoDomesticCost () > 0 ? $_product->getBongoDomesticCost () : $domestic_default) * $item->getQty ();
				}
			}
		} else if (! in_array ( $domestic_method, array ('bongocheckout_free', 'bongocheckout_manual' ) )) {
			try {
				$quote = Mage::getModel ( 'sales/quote' );
				$quote->setData ( Mage::getSingleton ( 'checkout/session' )->getQuote ()->getData () );
				$address = $quote->getShippingAddress ();
				$street = array (Mage::getStoreConfig ( 'bongocheckout_config/dc/address1' ) );
				$address2 = Mage::getStoreConfig ( 'bongocheckout_config/dc/address2' );
				
				if (! empty ( $address2 )) {
					$street [] = $address2;
				}
				
				$address->setCountryId ( Mage::getStoreConfig ( 'bongocheckout_config/dc/country' ) )->setRegionId ( Mage::getStoreConfig ( 'bongocheckout_config/dc/state' ) )->setRegion ( '' )->setCity ( Mage::getStoreConfig ( 'bongocheckout_config/dc/city' ) )->setPostcode ( Mage::getStoreConfig ( 'bongocheckout_config/dc/zip' ) )->setStreet ( $street );
				$address->setCollectShippingRates ( true );
				$address->collectTotals ();
				$rate = $address->getShippingRateByCode ( $domestic_method );
				$domestic_shipping = $rate->getPrice ();
			} catch ( Exception $err ) {
			}
		}
		
		return $domestic_shipping;
	}
	
	public function isSimpleProduct($product) {
		if ($product->getTypeId () == 'configurable' || $product->getTypeId () == 'bundle' || $product->getTypeId () == 'grouped') {
			return false;
		}
		
		return true;
	}
	
	public function getAllowedMethods() {
		return array ($this->_code => $this->getConfigData ( 'name' ) );
	}
}