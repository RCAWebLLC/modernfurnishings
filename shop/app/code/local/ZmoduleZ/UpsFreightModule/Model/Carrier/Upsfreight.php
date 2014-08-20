<?php

/**
 * |___  /                   | |     | |    |___  /                     
 *    / / _ __ ___   ___   __| |_   _| | ___   / /   ___ ___  _ __ ___  
 *   / / | '_ ` _ \ / _ \ / _` | | | | |/ _ \ / /   / __/ _ \| '_ ` _ \ 
 *  / /__| | | | | | (_) | (_| | |_| | |  __// /__ | (_| (_) | | | | | |
 * /_____|_| |_| |_|\___/ \__,_|\__,_|_|\___/_____(_)___\___/|_| |_| |_|
 * 
 * 
 * Ups Freight Shipping Extension
 *
 * @category   ZmoduleZ
 * @package    ZmoduleZ_UpsFreightModule
 * @copyright  Copyright (c) 2009 ZmoduleZ (http://www.zmodulez.com)
 * @license    http://www.zmodulez.com/license/license.txt
 * @author     sales@zmodulez.com>
 * @version    1.6.0
 */
class ZmoduleZ_UpsFreightModule_Model_Carrier_Upsfreight extends ZmoduleZ_UpsFreightModule_Model_Carrier_Abstractupsfreight {

	protected $_code = 'upsfreight';

	protected function _getCarrierCode(){
		Mage::log("getting carrier code...");	
		return 'upsfreight';
	}
	
	protected function _getOpco(){
		Mage::log("getting code for ups freight");
		return "FXF";
	}
}