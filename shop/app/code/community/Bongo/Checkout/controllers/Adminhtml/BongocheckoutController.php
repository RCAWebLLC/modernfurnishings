<?php

class Bongo_Checkout_Adminhtml_BongocheckoutController extends Mage_Adminhtml_Controller_Action {
	public function syncAction() {
		if (! $this->_checkActive ()) {
			return false;
		}
		
		$response = Mage::getModel ( 'bongocheckout/products_sync' )->sync ( true );
		
		if ($response->error > 0) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( "Error {$response->error}: {$response->errorMessage} - {$response->errorMessageDetail}" );
		} else {
			Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'bongocheckout' )->__ ( 'Your products have been successfully synchronized to Bongo!' ) );
		}
		
		session_write_close ();
		$this->_redirect ( 'adminhtml/system_config/edit/section/bongocheckout_products' );
	}
	
	public function exportAction() {
		if (! $this->_checkActive ()) {
			return false;
		}
		
		$export = 'language,productId,productDescription,url,imageUrl,price,originCountry,hsCode,ECCN,haz,licenseFlag,importFlag,productType,L1,W1,H1,WT1,L2,W2,H2,WT2,L3,W3,H3,WT3,L4,W4,H4,WT4' . "\n";
		$products = Mage::getModel ( 'bongocheckout/products_sync' )->sync ( false );
		
		foreach ( $products as $prod ) {
			$export .= 'en,"' . str_replace ( '"', '""', $prod ['productID'] ) . '","' . str_replace ( '"', '""', $prod ['description'] ) . '","' . str_replace ( '"', '""', $prod ['url'] ) . '","' . str_replace ( '"', '""', $prod ['imageUrl'] ) . '","' . $prod ['price'] . '","' . $prod ['countryOfOrigin'] . '","' . $prod ['hsCode'] . '","' . $prod ['eccn'] . '","' . $prod ['hazFlag'] . '","' . $prod ['licenseFlag'] . '","' . $prod ['importFlag'] . '","' . $prod ['productType'] . '",,,,"' . $prod ['itemInformation'] [0] ['wt'] . '",,,,,,,,,,,,' . "\n";
		}
		
		header ( 'Content-Type: application/csv' );
		header ( 'Content-Disposition: attachment; filename=productexport_' . date ( 'Y-m-d' ) . '.csv' );
		header ( 'Pragma: no-cache' );
		
		echo $export;
		exit ();
	}
	
	protected function _checkActive() {
		if (! Mage::getStoreConfig ( 'bongocheckout_config/config/active' )) {
			Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'bongocheckout' )->__ ( 'This action cannot be completed because the Bongo Checkout module is currently Disabled.' ) );
			
			session_write_close ();
			$this->_redirect ( 'adminhtml/system_config/edit/section/bongocheckout_products' );
			
			return false;
		}
		
		return true;
	}
}