<?php
 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2011 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 3.0
 * @since        Class available since Release 3.0
 */

class GoMage_Navigation_Model_Mysql4_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
	protected $_isPkAutoIncrement = false;
	
    public function _construct()
    {
        $this->_init('gomage_navigation/attribute', 'attribute_id');
    }       
}