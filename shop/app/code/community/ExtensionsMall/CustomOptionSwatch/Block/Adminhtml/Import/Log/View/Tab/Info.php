<?php

class ExtensionsMall_CustomOptionSwatch_Block_Adminhtml_Import_Log_View_Tab_Info extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    protected function _construct() {
        parent::_construct();
        #  $this->setTemplate('customoptionswatch/import/log/view/info.phtml');
    }

    public function getLog() {
        $logId = $this->getRequest()->getParam('log_id');
        $log = Mage::getModel('custom_option_swatch/log')->load($logId);
        return $log;
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel() {
        return Mage::helper('custom_option_swatch')->__('Information');
    }

    public function getTabTitle() {
        return Mage::helper('custom_option_swatch')->__('Order Information');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

}
