<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * MageWorx Adminhtml extension
 *
 * @category   MageWorx
 * @package    MageWorx_Adminhtml
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_Adminhtml_Block_Downloads_Categories_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'id';
        $this->_blockGroup = 'mageworx';
        $this->_controller = 'downloads_categories';

        $this->_updateButton('save', 'label', $this->_getHelper()->__('Save Category'));

        $this->_updateButton('delete', '', array(
	        'label'   => $this->_getHelper()->__('Delete Category'),
	        'onclick' => 'deleteConfirm(\'' . $this->_getHelper()->__('All files inside will be moved to Default Category. Are you sure you want to proceed?') . '\', \'' . $this->getUrl('*/*/delete', array('id' => $this->getRequest()->getParam('id'))) . '\')',
	        'class'   => 'delete',
			'sort_order' => 10
        ));

        $this->_addButton('saveandcontinue', array(
            'label'   => $this->_getHelper()->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class'   => 'save',
			'sort_order' => 20
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action + 'back/edit/');
            }
        ";
    }

    private function _getHelper()
    {
        return Mage::helper('downloads');
    }

    public function getHeaderText()
    {
        if (Mage::registry('downloads_data') && Mage::registry('downloads_data')->getId()) {
            return $this->_getHelper()->__("Edit Category '%s'", $this->htmlEscape(Mage::registry('downloads_data')->getTitle()));
        } else {
            return $this->_getHelper()->__('Add Category');
        }
    }
}