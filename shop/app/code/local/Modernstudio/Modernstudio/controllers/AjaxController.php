<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
class Modernstudio_Modernstudio_AjaxController extends Mage_Core_Controller_Front_Action {

    protected $_holes;
    
    public function holepunchAction() {
        
        $this->_holes = new Varien_Object();
        
        $update = $this->getLayout()->getUpdate();
        $update->addHandle('default');
        
        $this->addActionLayoutHandles()
            ->loadLayoutUpdates()
            ->generateLayoutXml()
            ->generateLayoutBlocks();
            
        $this->_fillHole('cart_link', 'top.links');
        $this->_fillHole('global_notices', 'global_notices');
        $this->_fillHole('global_messages', 'global_messages');
        $this->_fillHole('messages', 'messages');
        
        if ($this->getRequest()->getParam('cart')) {
            $this->_fillHole('cart', 'cart_sidebar');
        }
        
        if ($this->getRequest()->getParam('compare')) {
            $this->_fillHole('compare', 'catalog.compare.sidebar');
        }
        
        $out = Zend_Json::encode($this->_holes->getData());
        $this->getResponse()
            //->setHeader('Content-type', 'application/json')
            ->setBody($out);
    }
    
    protected function _fillHole($fieldName, $blockname) {
        if ($block = $this->getLayout()->getBlock($blockname)) {
            $this->_holes->{'set'.$fieldName}($block->renderView());
        }
    }
}
