<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Block_Adminhtml_Amazon_Listing_Edit_Tabs_Settings extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();

        // Initialization block
        //------------------------------
        $this->setId('amazonListingEditTabsSettings');
        //------------------------------

        $this->setTemplate('M2ePro/amazon/listing/tabs/settings.phtml');
    }

    protected function _beforeToHtml()
    {
        //-------------------------------
        $maxRecordsQuantity = Mage::helper('M2ePro/Module')->getConfig()
                                                           ->getGroupValue('/autocomplete/', 'max_records_quantity');
        $maxRecordsQuantity <= 0 && $maxRecordsQuantity = 100;
        //-------------------------------

        // Get attribute sets
        //------------------------------
        $this->attributesSets = Mage::helper('M2ePro/Magento')->getAttributeSets();

        //------------------------------
        $buttonBlock = $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData( array(
                'id'      => 'attribute_sets_select_all_button',
                'label'   => Mage::helper('M2ePro')->__('Select All'),
                'onclick' => 'AttributeSetHandlerObj.selectAllAttributeSets();',
                'class'   => 'attribute_sets_select_all_button'
            ) );
        $this->setChild('attribute_sets_select_all_button',$buttonBlock);

        $buttonBlock = $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData( array(
                'id'      => 'attribute_sets_confirm_button',
                'label'   => Mage::helper('M2ePro')->__('Confirm'),
                'onclick' => 'AmazonListingSettingsHandlerObj.attribute_sets_confirm();',
                'class'   => 'attribute_sets_confirm_button',
                'style'   => 'display: none'
            ) );
        $this->setChild('attribute_sets_confirm_button',$buttonBlock);
        //------------------------------

        //----------------------------
        $this->sellingFormatTemplatesDropDown = Mage::helper('M2ePro/Component_Amazon')
                                                    ->getCollection('Template_SellingFormat')
                                                    ->getSize() < $maxRecordsQuantity;
        $this->descriptionsTemplatesDropDown = Mage::helper('M2ePro/Component_Amazon')
                                                    ->getCollection('Template_Description')
                                                    ->getSize() < $maxRecordsQuantity;
        //----------------------------

        //----------------------------
        $synchronizationTemplatesCollection = Mage::helper('M2ePro/Component_Amazon')
                                                    ->getCollection('Template_Synchronization')
                                                    ->setOrder('title', 'ASC');

        if ($synchronizationTemplatesCollection->getSize() < $maxRecordsQuantity) {
            $this->synchronizationsTemplatesDropDown = true;
            $templates = $synchronizationTemplatesCollection->toArray();

            foreach ($templates['items'] as $key => $value) {
                $templates['items'][$key]['title'] = Mage::helper('M2ePro')
                                                                ->escapeHtml($templates['items'][$key]['title']);
            }

            $this->synchronizationsTemplates = $templates['items'];
        } else {
            $this->synchronizationsTemplatesDropDown = false;
            $this->synchronizationsTemplates = array();
        }
        //----------------------------

        // Get selected categories
        //----------------------------
        if ($listingId = $this->getRequest()->getParam('id')) {
            $listingsModel = Mage::getModel('M2ePro/Listing')->loadInstance($listingId);
            $listingCategories = $listingsModel->getCategories();
            $storeId = $listingsModel->getStoreId();

            // Getting paths for categories ids
            //----------------------------
            $categoriesPaths = array();
            $categoriesPathIds = array();

            foreach ($listingCategories as $listingCategory) {
                $tempItemPath = array_slice(explode('/', $listingCategory['path_ids']), 1);
                $categoriesPaths[] = $tempItemPath;
                $categoriesPathIds = array_merge($tempItemPath, $categoriesPathIds);
            }

            $categoriesPathIds = array_unique($categoriesPathIds);
            //----------------------------

            // Getting names for categories in paths and making breadcrumbs
            //----------------------------
            $collection = Mage::getModel('catalog/category')->getCollection()
                                                            ->setProductStoreId($storeId)
                                                            ->setStoreId($storeId)
                                                            ->addAttributeToSelect('name')
                                                            ->addIdFilter($categoriesPathIds);

            $categoryItems = $collection->getItems();
            $selectedCategories = array();

            foreach ($categoriesPaths as $categoryPath) {
                $breadcrumb = array();

                foreach ($categoryPath as $categoryId) {
                    if (isset($categoryItems[$categoryId]) &&
                        !is_null($categoryName = $categoryItems[$categoryId]->getName())
                    ) {
                        $breadcrumb[] = $categoryName;
                    }
                }

                if (count($breadcrumb)) {
                    $breadcrumb = implode(' > ', $breadcrumb);
                    $selectedCategories[] = $breadcrumb;
                }
            }
            //----------------------------

            $this->selectedCategories = $selectedCategories;
        }
        //----------------------------

        //------------------------------
        $buttonBlock = $this->getLayout()
                            ->createBlock('adminhtml/widget_button')
                            ->setData( array(
                                'label'   => Mage::helper('M2ePro')->__('Add New'),
                                'onclick' => 'AmazonListingSettingsHandlerObj.openWindow(\''
                                             .$this->getUrl('*/adminhtml_amazon_template_sellingFormat/new')
                                             .'\');',
                                'class'   => 'add add_new_selling_format_template_button',
                                'style'   => 'vertical-align: bottom'
                            ) );
        $this->setChild('add_new_selling_format_template_button',$buttonBlock);

        $buttonBlock = $this->getLayout()
                            ->createBlock('adminhtml/widget_button')
                            ->setData( array(
                                'label'   => Mage::helper('M2ePro')->__('Refresh'),
                                'onclick' => 'AmazonListingSettingsHandlerObj.reloadSellingFormatTemplates();',
                                'class'   => 'reload_selling_format_templates_button',
                                'style'   => 'vertical-align: bottom'
                            ) );
        $this->setChild('reload_selling_format_templates_button',$buttonBlock);
        //------------------------------

        //------------------------------
        $buttonBlock = $this->getLayout()
                            ->createBlock('adminhtml/widget_button')
                            ->setData( array(
                                'label'   => Mage::helper('M2ePro')->__('Add New'),
                                'onclick' => 'AmazonListingSettingsHandlerObj.openWindow(\''
                                             .$this->getUrl('*/adminhtml_amazon_template_description/new')
                                             .'\');',
                                'class'   => 'add add_new_description_template_button',
                                'style'   => 'vertical-align: bottom'
                            ) );
        $this->setChild('add_new_description_template_button',$buttonBlock);

        $buttonBlock = $this->getLayout()
                            ->createBlock('adminhtml/widget_button')
                            ->setData( array(
                                'label'   => Mage::helper('M2ePro')->__('Refresh'),
                                'onclick' => 'AmazonListingSettingsHandlerObj.reloadDescriptionTemplates();',
                                'class'   => 'reload_description_templates_button',
                                'style'   => 'vertical-align: bottom'
                            ) );
        $this->setChild('reload_description_templates_button',$buttonBlock);
        //------------------------------

        //------------------------------
        $buttonBlock = $this->getLayout()
                            ->createBlock('adminhtml/widget_button')
                            ->setData( array(
                                'label'   => Mage::helper('M2ePro')->__('Add New'),
                                'onclick' => 'AmazonListingSettingsHandlerObj.openWindow(\''
                                             .$this->getUrl('*/adminhtml_amazon_template_synchronization/new')
                                             .'\');',
                                'class'   => 'add add_new_synchronization_template_button',
                                'style'   => 'vertical-align: bottom'
                            ) );
        $this->setChild('add_new_synchronization_template_button',$buttonBlock);

        $buttonBlock = $this->getLayout()
                            ->createBlock('adminhtml/widget_button')
                            ->setData( array(
                                'label'   => Mage::helper('M2ePro')->__('Refresh'),
                                'onclick' => 'AmazonListingSettingsHandlerObj.reloadSynchronizationTemplates();',
                                'class'   => 'reload_synchronization_templates_button',
                                'style'   => 'vertical-align: bottom'
                            ) );
        $this->setChild('reload_synchronization_templates_button',$buttonBlock);
        //------------------------------

        return parent::_beforeToHtml();
    }
}