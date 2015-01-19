<?php
class Bongo_Checkout_Block_Adminhtml_ExportButton extends Mage_Adminhtml_Block_System_Config_Form_Field {
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		$this->setElement ( $element );
		$url = $this->getUrl ( 'adminhtml/bongocheckout/export' );
		
		$html = $this->getLayout ()->createBlock ( 'adminhtml/widget_button' )->setType ( 'button' )->setClass ( 'scalable' )->setLabel ( Mage::helper ( 'bongocheckout' )->__ ( 'Export Now!' ) )->setOnClick ( "setLocation('$url')" )->toHtml ();
		
		return $html;
	}
}