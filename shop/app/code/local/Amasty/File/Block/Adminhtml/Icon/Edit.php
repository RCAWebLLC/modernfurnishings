<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_File
 */

class Amasty_File_Block_Adminhtml_Icon_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {

       parent::__construct(); 
        $this->_objectId = 'id';
        $this->_blockGroup = 'amfile';
        $this->_controller = 'adminhtml_icon';
   
        $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('salesrule')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class' => 'save'
            ), 10);
       $this->_formScripts[] = " function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'back/edit') } ";
       $this->_formScripts[] = " function showOptions(sel) {
            new Ajax.Request('" . $this->getUrl('*/*/options', array('isAjax'=>true)) ."', {
                parameters: {code : sel.value},
                onSuccess: function(transport) {
                    $('attr_value').up().update(transport.responseText);
                }
            });
        }";
    }

    public function getHeaderText()
    {
        $header = Mage::helper('amfile')->__('New Product Icon');
        if (Mage::registry('amfile_icon')->getId()){
            $header = Mage::helper('amfile')->__('Edit Product Icon ');
        }
        return $header;
    }
}
