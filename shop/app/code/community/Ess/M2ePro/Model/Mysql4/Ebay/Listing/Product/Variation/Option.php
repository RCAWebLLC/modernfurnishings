<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Mysql4_Ebay_Listing_Product_Variation_Option
extends Ess_M2ePro_Model_Mysql4_Component_Child_Abstract
{
    protected $_isPkAutoIncrement = false;

    public function _construct()
    {
        $this->_init('M2ePro/Ebay_Listing_Product_Variation_Option', 'listing_product_variation_option_id');
        $this->_isPkAutoIncrement = false;
    }
}