<?php
class Bongo_Checkout_Model_Adminhtml_SyncFrequency
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('bongocheckout')->__('Nightly')),
            array('value'=>2, 'label'=>Mage::helper('bongocheckout')->__('Weekly')),
            array('value'=>3, 'label'=>Mage::helper('bongocheckout')->__('Monthly')),
        );
    }

}