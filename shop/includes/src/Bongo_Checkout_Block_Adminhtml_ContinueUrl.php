<?php
class Bongo_Checkout_Block_Adminhtml_ContinueUrl extends Mage_Adminhtml_Block_System_Config_Form_Field {
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		$this->setElement ( $element );
		$url = Mage::getUrl ( 'bongocheckout/continue' );
		
		$html = <<<END
<input type="text" value="{$url}" readonly="readonly" class=" input-text" onclick="this.select()" />
END;
		
		return $html;
	}
}