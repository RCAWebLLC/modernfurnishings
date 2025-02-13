<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Block_Adminhtml_Ebay_Account_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        // Initialization block
        //------------------------------
        $this->setId('ebayAccountEdit');
        $this->_blockGroup = 'M2ePro';
        $this->_controller = 'adminhtml_ebay_account';
        $this->_mode = 'edit';
        //------------------------------

        // Set header text
        //------------------------------
        if (count(Mage::helper('M2ePro/Component')->getActiveComponents()) > 1) {
            $componentName = ' ' . Ess_M2ePro_Helper_Component_Ebay::TITLE;
        } else {
            $componentName = '';
        }

        if (Mage::helper('M2ePro')->getGlobalValue('temp_data') &&
            Mage::helper('M2ePro')->getGlobalValue('temp_data')->getId()) {
            $headerText = Mage::helper('M2ePro')->__("Edit%component_name% Account");
            $headerText .= ' "'.$this->escapeHtml(Mage::helper('M2ePro')->getGlobalValue('temp_data')->getTitle()).'"';
        } else {
            $headerText = Mage::helper('M2ePro')->__("Add%component_name% Account");
        }
        $this->_headerText = str_replace('%component_name%',$componentName,$headerText);
        //------------------------------

        // Set buttons actions
        //------------------------------
        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('add');
        $this->removeButton('save');
        $this->removeButton('edit');

        if (Mage::getSingleton('M2ePro/Wizard')->isInstallationActive() &&
            Mage::getSingleton('M2ePro/Wizard')->getStatus() == Ess_M2ePro_Model_Wizard::STATUS_ACCOUNTS_EBAY) {

            $this->_addButton('reset', array(
                'label'     => Mage::helper('M2ePro')->__('Refresh'),
                'onclick'   => 'EbayAccountHandlerObj.reset_click()',
                'class'     => 'reset'
            ));

            $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('M2ePro')->__('Save And Continue Edit'),
                'onclick'   => 'EbayAccountHandlerObj.save_and_edit_click(\'\',\'ebayAccountEditTabs\')',
                'class'     => 'save'
            ));

            if ($this->getRequest()->getParam('id')) {

                $this->_addButton('add_new_account', array(
                    'label'     => Mage::helper('M2ePro')->__('Add New Account'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/adminhtml_ebay_account/new').'\')',
                    'class'     => 'add_new_account'
                ));

                $this->_addButton('close', array(
                    'label'     => Mage::helper('M2ePro')->__('Complete This Step'),
                    'onclick'   => 'EbayAccountHandlerObj.completeStep();',
                    'class'     => 'close'
                ));
            }

        } else {

            $this->_addButton('back', array(
                'label'     => Mage::helper('M2ePro')->__('Back'),
                'onclick'   => 'EbayAccountHandlerObj.back_click(\'' .Mage::helper('M2ePro')->getBackUrl('list').'\')',
                'class'     => 'back'
            ));

            $this->_addButton('reset', array(
                'label'     => Mage::helper('M2ePro')->__('Refresh'),
                'onclick'   => 'EbayAccountHandlerObj.reset_click()',
                'class'     => 'reset'
            ));

            if (Mage::helper('M2ePro')->getGlobalValue('temp_data') &&
                Mage::helper('M2ePro')->getGlobalValue('temp_data')->getId()) {

                $this->_addButton('delete', array(
                    'label'     => Mage::helper('M2ePro')->__('Delete'),
                    'onclick'   => 'EbayAccountHandlerObj.delete_click()',
                    'class'     => 'delete M2ePro_delete_button'
                 ));

                $this->_addButton('save', array(
                    'label'     => Mage::helper('M2ePro')->__('Save'),
                    'onclick'   => 'EbayAccountHandlerObj.save_click()',
                    'class'     => 'save'
                ));
            }

            $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('M2ePro')->__('Save And Continue Edit'),
                'onclick'   => 'EbayAccountHandlerObj.save_and_edit_click(\'\',\'ebayAccountEditTabs\')',
                'class'     => 'save'
            ));
        }
        //------------------------------
    }
}