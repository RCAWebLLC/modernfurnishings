<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Block_Adminhtml_Ebay_Listing_Add_StepTwoCategory extends Mage_Adminhtml_Block_Widget_View_Container
{
    public function __construct()
    {
        parent::__construct();

        // Initialization block
        //------------------------------
        $this->setId('ebayListingAddStepTwoCategory');
        $this->_blockGroup = 'M2ePro';
        $this->_controller = 'adminhtml_ebay_listing';
        //------------------------------

        // Set header text
        //------------------------------
        if (count(Mage::helper('M2ePro/Component')->getActiveComponents()) > 1) {
            $componentName = ' ' . Ess_M2ePro_Helper_Component_Ebay::TITLE;
        } else {
            $componentName = '';
        }

        $headerText = Mage::helper('M2ePro')->__("Add%component_name% Listing [Select Categories]");
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

        $url = $this->getUrl('*/adminhtml_ebay_listing/add',array('step'=>'1'));
        $this->_addButton('back', array(
            'label'     => Mage::helper('M2ePro')->__('Back'),
            'onclick'   => 'EbayListingCategoryHandlerObj.back_click(\'' .$url.'\')',
            'class'     => 'back'
        ));

        $this->_addButton('reset', array(
            'label'     => Mage::helper('M2ePro')->__('Refresh'),
            'onclick'   => 'EbayListingCategoryHandlerObj.reset_click()',
            'class'     => 'reset'
        ));

        $url = $this->getUrl('*/adminhtml_ebay_listing/add',array('step'=>'2','remember_categories'=>'yes'));
        $this->_addButton('save_and_next', array(
            'label'     => Mage::helper('M2ePro')->__('Next'),
            'onclick'   => 'EbayListingCategoryHandlerObj.save_click(\''.$url.'\')',
            'class'     => 'next save_and_next_button'
        ));

        $url = $this->getUrl('*/adminhtml_ebay_listing/add',array('step'=>'2','save'=>'yes','back'=>'list'));
        $this->_addButton('save_and_go_to_listings_list', array(
            'label'     => Mage::helper('M2ePro')->__('Save'),
            'onclick'   => 'EbayListingCategoryHandlerObj.save_click(\'' . $url .'\')',
            'class'     => 'save save_and_go_to_listings_list_button'
        ));

        $this->getUrl('*/adminhtml_ebay_listing/add',array('step'=>'2','save'=>'yes','back'=>'view'));
        $this->_addButton('save_and_go_to_listing_view', array(
            'label'     => Mage::helper('M2ePro')->__('Save And View Listing'),
            'onclick'   => 'EbayListingCategoryHandlerObj.save_click(\'' . $url .'\')',
            'class'     => 'save save_and_go_to_listing_view_button'
        ));
        //------------------------------
    }

    protected function _toHtml()
    {
        $categoryTreeBlock = $this->getLayout()
                                  ->createBlock('M2ePro/adminhtml_listing_category_tree','',array(
                                        'component'=>Ess_M2ePro_Helper_Component_Ebay::NICK
                                  ));
        $helpBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_ebay_listing_category_help');
        $categoryBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_ebay_listing_category');

        return parent::_toHtml() . $helpBlock->toHtml() . $categoryTreeBlock->toHtml() . $categoryBlock->toHtml();
    }
}