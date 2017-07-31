<?php
class Magehit_CallForPrice_FormController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_EMAIL_RECIPIENT  = 'callforprice/config/send_email_to';
    const XML_PATH_EMAIL_SENDER     = 'callforprice/config/email_sender';
    const XML_PATH_EMAIL_TEMPLATE   = 'callforprice/config/email_template';
    
    public function loadformAction()
    {
        $success = true;
        $product = Mage::getModel('catalog/product')->load($this->getRequest()->getPost('proId'));
        $request_form = $this->getLayout()->createBlock('magehit_callforprice/form')->assign('product',$product)->setTemplate('magehit/callforprice/callforprice_form.phtml')->toHtml();
        //return $request_form;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($request_form));
    }
    
    public function submitAction()
    {
        if ($this->getRequest()->getPost())
        {
            $cp = Mage::getModel('magehit_callforprice/request');
            $name = $this->getRequest()->getPost('name');
            $email = $this->getRequest()->getPost('email');
            $telephone = $this->getRequest()->getPost('telephone');
            $product_id = $this->getRequest()->getPost('product_id');
            $product = Mage::getModel('catalog/product')->load($product_id);
            $cp->setCustomerName($name)
                ->setCustomerEmail($email)
                ->setCustomerTelephone($telephone)
				->setStatus(1) // Set Status to New
                ->setProductId($product_id);
			
            $details = $this->getRequest()->getPost('details');
            $cp->setProductName($details);
            $store = Mage::app()->getStore(Mage::app()->getStore()->getStoreId());
            $detail = "<p>Product Name: ".$product->getName()."</p>";
            $detail .= "<p>Product Sku: ".$product->getSku()."</p>";
            $detail .= $details;
            try
            {
                $cp->save();
		
				$data = array();
						$data['name'] = $name;
						$data['email'] = $email;
						$data['telephone'] = $telephone;
                        $data['details'] = $detail;
				$data['status']= "New";
				
				$postObject = new Varien_Object();
						$postObject->setData($data);
				
				$translate  = Mage::getSingleton('core/translate');
				$mailTemplate = Mage::getModel('core/email_template');
				
				$mailTemplate->setReplyTo($email)->sendTransactional(
					Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
					Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
					Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
					null,
					array('data' => $postObject)
				);
				$translate->setTranslateInline(true);  
		
                $success =true; 
                $message = $this->__('Your request is accepted.');
            }
            catch (Exception $e)
            {
                $success =false;
                $message = $this->__('Your Request is Not Sent.');
            }

            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('success' => $success, 'message' => $message,)));
        }
    }
}
?>