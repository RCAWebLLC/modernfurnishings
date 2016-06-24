<?php
class Magehit_CallForPrice_Block_Adminhtml_Request_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
		public function __construct()
		{
				parent::__construct();
				$this->setId("request_tabs");
				$this->setDestElementId("edit_form");
				$this->setTitle(Mage::helper("magehit_callforprice")->__("Request Information"));
		}
		protected function _beforeToHtml()
		{
				$this->addTab("form_section", array(
				"label" => Mage::helper("magehit_callforprice")->__("Request Information"),
				"title" => Mage::helper("magehit_callforprice")->__("Request Information"),
				"content" => $this->getLayout()->createBlock("magehit_callforprice/adminhtml_request_edit_tab_form")->toHtml(),
				));
				return parent::_beforeToHtml();
		}

}
