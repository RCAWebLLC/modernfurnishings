<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Mysql4_Ebay_Listing_Product_Collection
extends Ess_M2ePro_Model_Mysql4_Collection_Component_Child_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Ebay_Listing_Product');
    }
}