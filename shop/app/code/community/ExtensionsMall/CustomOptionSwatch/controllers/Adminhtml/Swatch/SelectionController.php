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
class ExtensionsMall_CustomOptionSwatch_Adminhtml_Swatch_SelectionController extends Mage_Adminhtml_Controller_Action {

    protected function _construct() {
        $this->setUsedModuleName('ExtensionsMall_CustomOptionSwatch');
    }

    public function searchAction() {
        return $this->getResponse()->setBody(
                        $this->getLayout()
                                ->createBlock('custom_option_swatch/adminhtml_custom_option_swatch_selection_search')
                                ->setIndex($this->getRequest()->getParam('index'))
                                ->setFirstShow(true)
                                ->toHtml()
        );
    }

    public function gridAction() {
        return $this->getResponse()->setBody(
                        $this->getLayout()
                                ->createBlock('custom_option_swatch/adminhtml_custom_option_swatch_selection_search_grid', 'adminhtml.custom.option.swatch.selection.search.grid')
                                ->setIndex($this->getRequest()->getParam('index'))
                                ->toHtml()
        );
    }

}
