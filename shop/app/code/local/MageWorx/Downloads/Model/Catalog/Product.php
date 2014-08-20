<?php

class MageWorx_Downloads_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
    public function getAttributes($groupId = null, $skipSuper=false)
    {
        $attributes = parent::getAttributes($groupId, $skipSuper);
        foreach($attributes as $k => $attr){
            if($attr->getAttributeCode() == 'downloads_title'){
                unset($attributes[$k]);
                break;
            }
        }
        return $attributes;
    }
}