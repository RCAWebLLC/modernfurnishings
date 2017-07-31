<?php

class Magehit_CallForPrice_Block_Adminhtml_Request extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = "adminhtml_request";
        $this->_blockGroup = "magehit_callforprice";
        $this->_headerText = $this->__("Request Manager");
        parent::__construct();
        $this->_removeButton("add", "label", $this->__("Delete Item"));
    }
}