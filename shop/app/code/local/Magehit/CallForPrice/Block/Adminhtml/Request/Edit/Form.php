<?php
class Magehit_CallForPrice_Block_Adminhtml_Request_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        if (Mage::registry('request_data')) {
            $data = Mage::registry('request_data')->getData();
        } else {
            $data = array();
        }

        $form = new Varien_Data_Form(array(
                "id" => "edit_form",
                "action" => $this->getUrl("*/*/save", array("id" => $this->getRequest()->getParam("id"))),
                "method" => "post",
                "enctype" => "multipart/form-data",
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('request_data', array(
            'legend' => Mage::helper('magehit_callforprice')->__('Call For Price Request Information')
        ));

        $fieldset->addField('customer_name', 'text', array(
            'label' => Mage::helper('magehit_callforprice')->__('Customer Name'),
            'class' => '',
            'required' => false,
            'name' => 'customer_name',
            'note' => Mage::helper('magehit_callforprice')->__('Customer name.'),
        ));

        $fieldset->addField('customer_email', 'text', array(
            'label' => Mage::helper('magehit_callforprice')->__('Customer Email'),
            'class' => '',
            'required' => false,
            'name' => 'customer_email',
        ));

        $fieldset->addField('customer_telephone', 'text', array(
            'label' => Mage::helper('magehit_callforprice')->__('Telephone'),
            'class' => '',
            'required' => false,
            'name' => 'customer_telephone',
        ));

        $fieldset->addField('product_name', 'textarea', array(
            'label' => Mage::helper('magehit_callforprice')->__('Request Details'),
            'class' => '',
            'required' => false,
            'name' => 'product_name',
        ));
        
        $fieldset->addField('reply_detail', 'textarea', array(
            'label' => Mage::helper('magehit_callforprice')->__('Request Details Replied'),
            'class' => '',
            'required' => false,
            'name' => 'reply_detail',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('magehit_callforprice')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('magehit_callforprice')->__('New'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('magehit_callforprice')->__('Replied'),
                ),
            ),
        ));

        $form->setValues($data);

        return parent::_prepareForm();
    }
}
