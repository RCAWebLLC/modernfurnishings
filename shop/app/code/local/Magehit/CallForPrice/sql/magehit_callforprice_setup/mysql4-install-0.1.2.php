<?php
$installer = $this;

$installer->startSetup();

$installer->getConnection()->dropTable($installer->getTable('magehit_callforprice/request'));

$table = $installer->getConnection()
    ->newTable($installer->getTable('magehit_callforprice/request'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
    ), 'ID')
    ->addColumn('customer_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false
    ), 'Name')                                        
    ->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false
    ), 'Email')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false
    ), 'Customer ID')
    ->addColumn('customer_telephone', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Customer Telephone')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false
    ), 'Product ID')
    ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT,null, array(
        'nullable'  => false
    ), 'Product Name')
    ->addColumn('reply_detail', Varien_Db_Ddl_Table::TYPE_TEXT,null, array(
        'nullable'  => false
    ), 'Request Details Replied')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false
    ), 'Status');

$installer->getConnection()->createTable($table);

// catalog_product
$installer->addAttribute('catalog_product', 'call_for_price_active', array(
    'group'              => 'Prices',
    'type'               => 'int',
    'backend'            => '',
    'frontend'           => '',
    'label'              => 'Call For Price',
    'input'              => 'select',
    'class'              => '',
    'source'             => 'eav/entity_attribute_source_boolean',
    'global'             => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'            => true,
    'required'           => false,
    'user_defined'       => false,
    'default'            => 'No',
    'searchable'         => false,
    'filterable'         => false,
    'comparable'         => false,
    'visible_on_front'   => false,
    'unique'             => false,
    'apply_to'           => '',
    'is_configurable'    => false,
    'is_visible_on_front'=> true,
    'used_in_product_listing' => true    
));

// catalog_category 
$installer->addAttribute("catalog_category", "call_for_price_active",  array(
    "type"     => "int",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Call For Price",
    "input"    => "select",
    "class"    => "",
    "source"   => "eav/entity_attribute_source_boolean",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "No",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,    
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => ""

    )); 
    
$installer->endSetup();