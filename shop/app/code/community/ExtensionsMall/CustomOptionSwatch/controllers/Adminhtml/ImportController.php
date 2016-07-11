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
class ExtensionsMall_CustomOptionSwatch_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action {

    /**
     * Init layout, menu and breadcrumb
     *
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('catalog')
                ->_addBreadcrumb($this->__('Catalog'), $this->__('Catalog'));
        return $this;
    }

    public function indexAction() {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('custom_option_swatch/adminhtml_import_log'));
        $this->renderLayout();
    }

    public function newAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction() {
        $this->_title($this->__('Log Details'));
        $this->_initAction();
        $this->renderLayout();
    }

    public function submitAction() {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $this->loadLayout(false);
            $importModel = Mage::getModel('custom_option_swatch/import');
            try {
                $importModel->processImport($data);
                $this->_redirect('*/*/index');
            } catch (Exception $e) {
                $this->renderLayout();
                return;
            }
        } else {
            $this->_redirect('*/*/index');
        }
    }

    public function deleteAction() {
        $log = Mage::getModel('custom_option_swatch/log')->load($this->getRequest()->getParam('log_id'));
        try {
            $log->delete();
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/view', array('log_id' => $this->getRequest()->getParam('log_id')));
        }
    }

}
