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
 * @version    1.2.0
 */
class ZmoduleZ_UpsFreightModule_Model_Carrier_UpsFreight_Source_Method
{
    public function toOptionArray()
    {
        $fedFreight = Mage::getSingleton('upsfreightmodule/carrier_upsfreight');
        $arr = array();
        foreach ($fedFreight->getCode('method') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>$v);
        }
        return $arr;
    }
}
