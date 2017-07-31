<?php

class ExtensionsMall_CustomOptionSwatch_Block_Adminhtml_Import_Log_View_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('custom_option_swatch_import_log_view_tabs');
        $this->setDestElementId('custom_option_swatch_import_log_view');
        $this->setTitle(Mage::helper('custom_option_swatch')->__('Log View'));
    }

    /*  public function __construct() {
      parent::__construct();
      $this->setId('import_log_view_tabs');
      $this->setDestElementId('log_id');
      $this->setTitle(Mage::helper('custom_option_swatch')->__('Information'));
      }

      protected function _beforeToHtml() {
      $this->addTab('info_section', array(
      'label' => Mage::helper('custom_option_swatch')->__('General Information'),
      'title' => Mage::helper('custom_option_swatch')->__('General Information'),
      ));

      return parent::_beforeToHtml();
      }
     */
}
