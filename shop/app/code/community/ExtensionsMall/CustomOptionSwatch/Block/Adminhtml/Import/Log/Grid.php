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
class ExtensionsMall_CustomOptionSwatch_Block_Adminhtml_Import_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('LogGrid');

        // Set some defaults for our grid
        $this->setDefaultSort('log_id');

        $this->setDefaultDir('asc');

        $this->setSaveParametersInSession(true);
    }

  /*  protected function _getCollectionClass() {
        // This is the model we are using for the grid

        return 'custom_option_swatch/log';
    }
*/
    protected function _prepareCollection() {
        // Get and set our collection for the grid
        $collection = Mage::getModel('custom_option_swatch/log')->getCollection();
        $this->setCollection($collection);

        /* $collection = Mage::getResourceModel($this->_getCollectionClass());
          $this->setCollection($collection);
         */
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        // Add the columns that should appear in the grid
        $this->addColumn('log_id', array(
            'header' => $this->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'log_id'
                )
        );

        $this->addColumn('num_submited', array(
            'header' => $this->__('num submited'),
            'index' => 'num_submited'
                )
        );
        $this->addColumn('num_processed', array(
            'header' => $this->__('num processed'),
            'index' => 'num_processed'
                )
        );
        $this->addColumn('created', array(
            'header' => $this->__('Created at'),
            'type' => 'datetime',
            'index' => 'created'
                )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        // This is where our row data will link to
        return $this->getUrl('*/*/view', array('log_id' => $row->getLogId()));
    }

}
