<?php
	
class Magehit_CallForPrice_Block_Adminhtml_Request_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

				parent::__construct();
				$this->_objectId = "id";
				$this->_blockGroup = "magehit_callforprice";
				$this->_controller = "adminhtml_request";
				$this->_updateButton("save", "label", Mage::helper("magehit_callforprice")->__("Save Item"));
				$this->_removeButton("delete", "label", Mage::helper("magehit_callforprice")->__("Delete Item"));

				$this->_addButton("saveandcontinue", array(
					"label"     => Mage::helper("magehit_callforprice")->__("Save And Continue Edit"),
					"onclick"   => "saveAndContinueEdit()",
					"class"     => "save",
				), -100);
                
                $this->_addButton("replyrequest", array(
                    "label"     => Mage::helper("magehit_callforprice")->__("Reply Request"),
                    "onclick"   => 'replyrequest()',
                    "class"     => "save",
                ), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
                            
                            function replyrequest(){
                                editForm.submit($('edit_form').action+'back/edit/reply/1');
                            }
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry("request_data") && Mage::registry("request_data")->getId() ){

				    return Mage::helper("magehit_callforprice")->__("Edit Request '%s'", $this->htmlEscape(Mage::registry("request_data")->getId()));

				} 
				else{

				     return Mage::helper("magehit_callforprice")->__("Add Request");

				}
		}
}