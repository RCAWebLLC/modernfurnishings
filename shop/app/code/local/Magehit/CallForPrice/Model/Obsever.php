<?php
class Magehit_CallForPrice_Model_Obsever{
    public function checkProductCallForPrice(Varien_Event_Observer $observer){
        $wishlist = Mage::helper('wishlist')->getWishlist();
        $collection = $wishlist->getItemCollection()
                ->setVisibilityFilter();
        $hasCallForPrice = false;
        foreach ($collection as $item) {
          $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
          $canshow = Mage::helper("magehit_callforprice")->showCallForPriceButton($product);
          if($canshow){
            $hasCallForPrice = true;
            continue;    
          }
        }
        if($hasCallForPrice){
            Mage::getSingleton('wishlist/session')->addError(Mage::helper("magehit_callforprice")->__('In your WishList has product is call for price. So you can not add it to cart.'));
            
        }
        $action = $observer->getEvent()->getControllerAction();
        $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        return Mage::app()->getResponse()->setRedirect(Mage::getUrl('wishlist'));
    } 
}
?>