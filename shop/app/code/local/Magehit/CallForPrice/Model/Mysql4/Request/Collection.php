<?php

class Magehit_CallForPrice_Model_Mysql4_Request_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        $this->_init("magehit_callforprice/request");
    }
}