<?php

class Magehit_CallForPrice_Adminhtml_RequestController extends Mage_Adminhtml_Controller_Action
{
        const XML_PATH_EMAIL_RECIPIENT  = 'callforprice/config/send_email_to';
        const XML_PATH_EMAIL_SENDER     = 'callforprice/config/email_sender';
        const XML_PATH_EMAIL_TEMPLATE   = 'callforprice/config/email_template';
        const XML_PATH_EMAIL_TEMPLATE_REPLY   = 'callforprice/config/email_template_reply';
		protected function _initAction()
		{
				$this->loadLayout()->_setActiveMenu("magehit_callforprice/request")->_addBreadcrumb(Mage::helper("adminhtml")->__("Request  Manager"),Mage::helper("adminhtml")->__("Request Manager"));
				return $this;
		}
		public function indexAction() 
		{
			    $this->_title($this->__("CallForPrice"));
			    $this->_title($this->__("Manager Request"));

				$this->_initAction();
				$this->renderLayout();
		}
		public function editAction()
		{			    
			    $this->_title($this->__("CallForPrice"));
				$this->_title($this->__("Request"));
			    $this->_title($this->__("Edit Item"));
				
				$id = $this->getRequest()->getParam("id");
				$model = Mage::getModel("magehit_callforprice/request")->load($id);
				if ($model->getId()) {
					Mage::register("request_data", $model);
					$this->loadLayout();
					$this->_setActiveMenu("magehit_callforprice/request");
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Request Manager"), Mage::helper("adminhtml")->__("Request Manager"));
					$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Request Description"), Mage::helper("adminhtml")->__("Request Description"));
					$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock("magehit_callforprice/adminhtml_request_edit"))->_addLeft($this->getLayout()->createBlock("magehit_callforprice/adminhtml_request_edit_tabs"));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton("adminhtml/session")->addError(Mage::helper("magehit_callforprice")->__("Item does not exist."));
					$this->_redirect("*/*/");
				}
		}

		public function newAction()
		{

		$this->_title($this->__("CallPrice"));
		$this->_title($this->__("Request"));
		$this->_title($this->__("New Item"));

        $id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("magehit_callforprice/request")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register("request_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("magehit_callforprice/request");

		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Request Manager"), Mage::helper("adminhtml")->__("Request Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Request Description"), Mage::helper("adminhtml")->__("Request Description"));


		$this->_addContent($this->getLayout()->createBlock("magehit_callforprice/adminhtml_request_edit"))->_addLeft($this->getLayout()->createBlock("magehit_callforprice/adminhtml_request_edit_tabs"));

		$this->renderLayout();

		}
		public function saveAction()
		{
			$post_data=$this->getRequest()->getPost();
				if ($post_data) {
					try {
                        if($this->getRequest()->getParam("reply"))
                        $post_data['status'] = "2";
                        
						$model = Mage::getModel("magehit_callforprice/request")
						->addData($post_data)
						->setId($this->getRequest()->getParam("id"))
						->save();
                        
                        if($this->getRequest()->getParam("reply")){
                            $data = array();
                            $data['name'] = $post_data["customer_name"];
                            $data['email'] = $post_data["customer_email"];
                            $data['telephone'] = $post_data["customer_telephone"];
                            $data['details'] = $post_data["product_name"];
                            $data['detailsreplied'] = $post_data["reply_detail"];
                            
                            $postObject = new Varien_Object();
                                    $postObject->setData($data);
                            
                            $translate  = Mage::getSingleton('core/translate');
                            $mailTemplate = Mage::getModel('core/email_template');
                            
                            $mailTemplate->setReplyTo($post_data["customer_email"])->sendTransactional(
                                Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE_REPLY),
                                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                                $post_data["customer_email"],
                                null,
                                array('data' => $postObject)
                            );
                            $translate->setTranslateInline(true);
                        }
                        if($this->getRequest()->getParam("reply"))
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Request was successfully replied"));
                        else
                        Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Request was successfully saved"));
                        
						Mage::getSingleton("adminhtml/session")->setRequestData(false);
						if ($this->getRequest()->getParam("back")) {
							$this->_redirect("*/*/edit", array("id" => $model->getId()));
							return;
						}
						$this->_redirect("*/*/");
						return;
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						Mage::getSingleton("adminhtml/session")->setRequestData($this->getRequest()->getPost());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					return;
					}

				}
				$this->_redirect("*/*/");
		}

		public function deleteAction()
		{
				if( $this->getRequest()->getParam("id") > 0 ) {
					try {
						$model = Mage::getModel("magehit_callforprice/request");
						$model->setId($this->getRequest()->getParam("id"))->delete();
						Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
						$this->_redirect("*/*/");
					} 
					catch (Exception $e) {
						Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
						$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
					}
				}
				$this->_redirect("*/*/");
		}

        public function massStatusUpdateAction()
        {
            $requestIds = $this->getRequest()->getParam('id');      // $this->getMassactionBlock()->setFormFieldName('tax_id'); from Mage_Adminhtml_Block_Tax_Rate_Grid
            if(!is_array($requestIds)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('magehit_callforprice')->__('Please select status.'));
            } else {
                try {
                    $model = Mage::getModel("magehit_callforprice/request");
                    foreach ($requestIds as $requestId) {
                        $model->load($requestId);
                        if ($model->getId()) {
                            $getstatus = $this->getRequest()->getParam("status");
                            $model->setStatus($getstatus);
                            $model->save();
                        }
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('magehit_callforprice')->__(
                            'Total of %d record(s) were updated.', count($requestIds)
                        )
                    );
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            $this->_redirect('*/*/');
        }

		
}
