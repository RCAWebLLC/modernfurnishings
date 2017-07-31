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
class ExtensionsMall_CustomOptionSwatch_Adminhtml_SwatchController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('catalog/custom_option_swatch_images')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Custom Option Swatch Images Manager'), Mage::helper('adminhtml')->__('Custom Option Swatch Images Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {

        $swatchId = $this->getRequest()->getParam('id');
        $swatch = Mage::getModel('custom_option_swatch/swatches')->load($swatchId);

        if ($swatch->getId() || $swatchId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $swatch->setData($data);
            }

            Mage::register('custom_option_swatch', $swatch);

            $this->loadLayout();
            $this->_setActiveMenu('catalog/custom_option_swatch_images');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('custom_option_swatch/adminhtml_swatches_edit'))
                    ->_addLeft($this->getLayout()->createBlock('custom_option_swatch/adminhtml_swatches_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('custom_option_swatch')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction() {

        if ($data = $this->getRequest()->getPost()) {

            $swatch = Mage::getModel('custom_option_swatch/swatches')->load($this->getRequest()->getParam('id'));
            if ($this->getRequest()->getParam('id') > 0 && !is_object($swatch)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('custom_option_swatch')->__('Unable to find item to save'));
                $this->_redirect('*/*/');
                return;
            }

            $swatch->setData(array_merge($swatch->getData(), $data));

            $tempFileName = false;
            if (isset($_FILES['image_base']['name']) && $_FILES['image_base']['name'] != '') {
                try {
                    $uploader = new Varien_File_Uploader('image_base');
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);

                    if ($swatch->getId() > 0) {
                        $uploader->save(Mage::getBaseDir('media') . DS . 'custom_option_swatch' . DS . $swatch->getId() . DS, $swatch->createFileName($_FILES['image_base']['name']));
                    } else {
                        $swatch->setTempImageBase(md5(rand(1000, 9999)) . '_' . $swatch->createFileName($_FILES['image_base']['name']));
                        $uploader->save(Mage::getBaseDir('media') . DS . 'custom_option_swatch' . DS, $swatch->getTempImageBase());
                    }

                    $swatch->setImageBase($swatch->createFileName($_FILES['image_base']['name']));
                } catch (Exception $e) {
                    throw new Exception('Unable to upoload swatch image');
                    $tempFileName = false;
                }
            }

            try {

                if ($swatch->getCreatedTime() == NULL || $swatch->getUpdateTime() == NULL) {
                    $swatch->setCreatedTime(now())->setUpdateTime(now());
                } else {
                    $swatch->setUpdateTime(now());
                }

                $swatch->save();
                if ($swatch->hasTempImageBase()) {
                    if (!is_dir(Mage::getBaseDir('media') . DS . 'custom_option_swatch' . DS . $swatch->getId() . DS)) {
                        mkdir(Mage::getBaseDir('media') . DS . 'custom_option_swatch' . DS . $swatch->getId() . DS, 0775);
                    }
                    try {
                        copy(Mage::getBaseDir('media') . DS . 'custom_option_swatch' . DS . $swatch->getTempImageBase(), Mage::getBaseDir('media') . DS . 'custom_option_swatch' . DS . $swatch->getId() . DS . $swatch->getImageBase());
                        unlink(Mage::getBaseDir('media') . DS . 'custom_option_swatch' . DS . $swatch->getTempImageBase());
                    } catch (Exception $e) {
                        throw new Exception('Unable to move upoload swatch image');
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('custom_option_swatch')->__('Swatch was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $swatch->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setSwatchData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('custom_option_swatch')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        $swatch = Mage::getModel('custom_option_swatch/swatches')->load($this->getRequest()->getParam('id'));
        try {
            $swatch->delete();
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        }
    }

}
