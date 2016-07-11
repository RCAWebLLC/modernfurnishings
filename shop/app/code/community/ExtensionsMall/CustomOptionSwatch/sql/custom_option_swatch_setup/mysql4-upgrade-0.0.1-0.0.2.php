<?php
$installer = $this;
$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('custom_option_swatch_images'), 'description', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true
    ));
$installer->endSetup();
