<?php

$installer = $this;
$product_installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');
$order_installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$installer->startSetup ();

if ($product_installer->getAttributeId ( 'catalog_product', 'bongo_domestic_cost' ) === false)
	$product_installer->addAttribute ( 'catalog_product', 'bongo_domestic_cost', array ('group' => 'Bongo Checkout', 'input' => 'price', 'type' => 'decimal', 'label' => 'Bongo Domestic Shipping Cost', 'note' => 'If you configure Bongo\'s Domestic Shipping Calculation to be "Per-Product", this value will be combined with the Calculated Shipping Cost returned from Bongo and the resulting total will be displayed in shipping estimates.', 'backend' => '', 'visible' => 1, 'required' => 0, 'user_defined' => 0, 'searchable' => 0, 'filterable' => 0, 'comparable' => 0, 'visible_on_front' => 0, 'visible_in_advanced_search' => 0, 'is_html_allowed_on_front' => 0, 'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE ) );
if ($order_installer->getAttributeId ( 'order', 'bongo_id' ) === false)
	$order_installer->addAttribute ( 'order', 'bongo_id', array ('type' => 'int', 'input' => 'text', 'label' => 'Bongo Order ID', 'visible' => true, 'required' => false, 'position' => 1 ) );
if ($order_installer->getAttributeId ( 'order', 'bongo_tracking' ) === false)
	$order_installer->addAttribute ( 'order', 'bongo_tracking', array ('type' => 'varchar', 'input' => 'text', 'label' => 'Bongo Tracking Link', 'visible' => true, 'required' => false, 'position' => 1 ) );
if ($order_installer->getAttributeId ( 'order', 'bongo_status_code' ) === false)
	$order_installer->addAttribute ( 'order', 'bongo_status_code', array ('type' => 'varchar', 'input' => 'text', 'label' => 'Bongo Latest Status Code', 'visible' => true, 'required' => false, 'position' => 1 ) );
if ($order_installer->getAttributeId ( 'order', 'bongo_status_date' ) === false)
	$order_installer->addAttribute ( 'order', 'bongo_status_date', array ('type' => 'timestamp', 'input' => 'text', 'label' => 'Bongo Latest Status Date', 'visible' => true, 'required' => false, 'position' => 1 ) );
if ($order_installer->getAttributeId ( 'order', 'bongo_landedcosttransactionid' ) === false)
	$order_installer->addAttribute ( 'order', 'bongo_landedcosttransactionid', array ('type' => 'varchar', 'input' => 'text', 'label' => 'Bongo LandedCostTransactionID', 'visible' => true, 'required' => false, 'position' => 1 ) );
if ($order_installer->getAttributeId ( 'order', 'bongo_totals_subtotal' ) === false)
	$order_installer->addAttribute ( 'order', 'bongo_totals_subtotal', array ('type' => 'decimal', 'input' => 'text', 'label' => 'Bongo Order Subtotal', 'visible' => true, 'required' => false, 'position' => 1 ) );
if ($order_installer->getAttributeId ( 'order', 'bongo_totals_duty' ) === false)
	$order_installer->addAttribute ( 'order', 'bongo_totals_duty', array ('type' => 'decimal', 'input' => 'text', 'label' => 'Bongo Shipping Duties', 'visible' => true, 'required' => false, 'position' => 1 ) );
if ($order_installer->getAttributeId ( 'order', 'bongo_totals_tax' ) === false)
	$order_installer->addAttribute ( 'order', 'bongo_totals_tax', array ('type' => 'decimal', 'input' => 'text', 'label' => 'Bongo Shipping Tax', 'visible' => true, 'required' => false, 'position' => 1 ) );
if ($order_installer->getAttributeId ( 'order', 'bongo_totals_shipping' ) === false)
	$order_installer->addAttribute ( 'order', 'bongo_totals_shipping', array ('type' => 'decimal', 'input' => 'text', 'label' => 'Bongo International Shipping', 'visible' => true, 'required' => false, 'position' => 1 ) );
if ($order_installer->getAttributeId ( 'order', 'bongo_totals_domestic' ) === false)
	$order_installer->addAttribute ( 'order', 'bongo_totals_domestic', array ('type' => 'decimal', 'input' => 'text', 'label' => 'Bongo Domestic Shipping', 'visible' => true, 'required' => false, 'position' => 1 ) );
if ($order_installer->getAttributeId ( 'order', 'bongo_totals_insurance' ) === false)
	$order_installer->addAttribute ( 'order', 'bongo_totals_insurance', array ('type' => 'decimal', 'input' => 'text', 'label' => 'Bongo Insurance', 'visible' => true, 'required' => false, 'position' => 1 ) );
if ($order_installer->getAttributeId ( 'order', 'bongo_totals_total' ) === false)
	$order_installer->addAttribute ( 'order', 'bongo_totals_total', array ('type' => 'decimal', 'input' => 'text', 'label' => 'Bongo Order Total', 'visible' => true, 'required' => false, 'position' => 1 ) );

$installer->endSetup ();
