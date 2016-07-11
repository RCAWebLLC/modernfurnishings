<?php
class Magehit_CallForPrice_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function enableModule($store)
    {
        return Mage::getStoreConfig('callforprice/config/enabled',$store);
    }
    
    public function loadformurl()
    {
		$isSecure = Mage::app()->getStore()->isCurrentlySecure(); 
		if($isSecure)
        $loadformurl = Mage::getUrl('callforprice/form/loadform',array('_secure'=>true));
		else
        $loadformurl = Mage::getUrl('callforprice/form/loadform');
        return $loadformurl;
    }

    public function submitformurl()
    {
		$isSecure = Mage::app()->getStore()->isCurrentlySecure(); 
		if($isSecure)
        $submitformurl = Mage::getUrl('callforprice/form/submit',array('_secure'=>true));
		else
        $submitformurl = Mage::getUrl('callforprice/form/submit');
        return $submitformurl;
    }

    public function getButtonTitle()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('callforprice/config/call_for_price_button_text',$storeId);
    }

    //check which customer group for which we will show call for price button
    public function allowedCustomerGroup()
    {
      $storeId = Mage::app()->getStore()->getStoreId();
      return Mage::getStoreConfig('callforprice/config/customer_groups',$storeId);
    }

    public function getCurrentCustomerGroup()
    {
        $login = Mage::getSingleton( 'customer/session' )->isLoggedIn(); //Check if User is Logged In
        if($login)
        {
            $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId(); //Get Customers Group ID
            return $groupId;
        }
    }

    public function getSpecificDateRange()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('callforprice/config/specific_date_range',$storeId);
    }
    public function getFromDate()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('callforprice/config/from_date',$storeId);
    }
    public function getToDate()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        return Mage::getStoreConfig('callforprice/config/to_date',$storeId);
    }

   public function getDateToShowButton()
    {
        //Get From To date
        if($this->getSpecificDateRange()==1)
        {
            $fromDate = date('m/d/Y',strtotime($this->getFromDate()));
            $toDate = date('m/d/Y',strtotime($this->getToDate()));
            $todayDate = date('m/d/Y');
            if(strtotime($todayDate)>=strtotime($fromDate) && strtotime($todayDate)<=strtotime($toDate))
            {
                return 1;
            }
            else return 0;
        }
        return 1;        
    }

    public function showCallForPriceButton($_product)
    {      
        $product = Mage::getModel('catalog/product')->load($_product->getId());  
        $callpriceflag = 0;
        $callpriceflagparent = 0;
        $callforprice = 0;
        $showCallForPriceButton = 0;
        $allowed_customer_groups = array();
        $customer_groups = $this->allowedCustomerGroup();  // Check customer Group
        if($customer_groups != "")
        {
            $allowed_customer_groups = explode(',',$customer_groups);
        }
        
        $c_group = $this->getCurrentCustomerGroup(); // current customer group id
        
        if(count($allowed_customer_groups) > 0)
        {
            if(in_array($c_group,$allowed_customer_groups))
            {
                $showCallForPriceButton = 1;
                return $showCallForPriceButton;            
            }
            else
            {
                $showCallForPriceButton = 0;
                
            }
        }
        
        $buttonDate = $this->getDateToShowButton();    
        if($buttonDate == 1){}
            //$showCallForPriceButton = 1;
            //return $showCallForPriceButton;            
        else return 0;
        
        $currentCategory = Mage::registry('current_category'); // check for current category
        if($currentCategory)
        {
            $cat = Mage::getModel('catalog/category')->load($currentCategory->getId());
            $callpriceflag = $cat->getCallForPriceActive();
                        
            if($cat->getParentCategory())
            {
                $callpriceflagparent = $cat->getParentCategory()->getCallForPriceActive();
            }
        }
        
        //check in page wishlist or compare
        $cats = $product->getCategoryIds();
        
        foreach ($cats as $category_id) {
            
            $_cat = Mage::getModel('catalog/category')->load($category_id) ;
            
            if($_cat->getParentCategory())
            {
                $callpriceflagparent = $_cat->getParentCategory()->getCallForPriceActive();
            }
        } 

        $callforprice = $product->getCallForPriceActive(); // Check for product   
          
        if($callforprice == 1||$callpriceflagparent == 1||$callpriceflag == 1)
        {
            $showCallForPriceButton = 1;            
        }
        return $showCallForPriceButton;
    }
    
    public function getButtonCallPriceTemplate($product){
        return Mage::app()->getLayout()->createBlock('core/template')->assign('product',$product)->setTemplate('magehit/callforprice/button.phtml')->toHtml();
    
    }

    public function getJsCallForPrice($productId,$itemIdWishList){
        $product = Mage::getModel('catalog/product')->load($productId);
        return Mage::app()->getLayout()->createBlock('core/template')->assign(array('product'=>$product,'wishId'=>$itemIdWishList))->setTemplate('magehit/callforprice/jscallforprice.phtml')->toHtml();
    
    }

}
     