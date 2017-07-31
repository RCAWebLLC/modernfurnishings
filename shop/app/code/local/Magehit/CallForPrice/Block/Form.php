<?php
class Magehit_CallForPrice_Block_Form extends Mage_Core_Block_Template
{
    public function getFormAction()
    {
    return Mage::getUrl('callforprice/form/submit');
    }

    public function customerLoggedIn()
    {
     return Mage::getSingleton('customer/session')->isLoggedIn();
    }
    public function buttonLabel()
    {
        $label = Mage::getStoreConfig('callforprice/config/call_for_price_button_text');

        return $label;
    }
    public function getProductId()
    {
       return $product_id = $this->getRequest()->getPost('product');
    }
}
