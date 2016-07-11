<?php

/**
 * ExtensionsMall
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ExtensionsMall EULA that is available through 
 * the world-wide-web at this URL: http://www.extensionsmall.com/license.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to support@extensionsmall.com for more information.
 *
 * @category   ExtensionsMall
 * @package    ExtensionsMall_CustomOptionSwatch
 * @author     ExtensionsMall Dev Team
 * @copyright  Copyright (c) 2015 ExtensionsMall (http://www.extensionsmall.com/)
 * @license    http://www.extensionsmall.com/license.html
 */
class ExtensionsMall_CustomOptionSwatch_Block_Adminhtml_Swatches_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('swatches_form', array('legend' => Mage::helper('custom_option_swatch')->__('Swatch information')));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('custom_option_swatch')->__('Label'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
        ));

	$fieldset->addField('description', 'textarea', array(
            'label' => Mage::helper('custom_option_swatch')->__('Description'),
            'class' => '',
            'required' => false,
            'name' => 'description',
        ));

        $eventElem = $fieldset->addField('image_base', 'file', array(
            'label' => Mage::helper('custom_option_swatch')->__('Swatch (Texture)'),
            'required' => Mage::registry('custom_option_swatch')->getId() > 0 ? false : true,
            'name' => 'image_base'
        ));



        $swatchImage = Mage::helper('custom_option_swatch/image')->init(Mage::registry('custom_option_swatch'));
        if ($swatchImage->isImageExsist()) {
            $eventElem->setAfterElementHtml("<br /><img title=\"" . Mage::registry('custom_option_swatch')->getName() . "\" style='margin-top:10px;' src=\"" . $swatchImage->resize(30) . "\"  /> ");
        }

        if (Mage::registry('custom_option_swatch')->getId() > 0) {
            $data = Mage::registry('custom_option_swatch')->getData();
            $form->setValues($data);
        } elseif (Mage::registry('custom_option_swatch')) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getSwatchData());
            Mage::getSingleton('adminhtml/session')->setSwatchData(null);
        }
        return parent::_prepareForm();
    }

}
