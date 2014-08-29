<?php

return array(
	'base_url' => 'http://modern-furnishings.com', // URL Clients will access
	'cookie_domain' => '.modern-furnishings.com',
	'mage_table_prefix' => '', // mage and ee table prefixes
	'ee_table_prefix' => 'exp_', // Yes, these are repeated below.
	'replacements' => array(
		// These are values to be replaced,
		// think replace each entry below with the corresponding entry from above.
		'docroot' => array(
			// These value will be replaced with the document root. The document root is auto detected at run time.
			'/var/www/vhosts/modern-furnishings.com/httpdocs/',
		),
		'base_url' => array(
			// These values will each be replaced with the base_url set above.
			'http://ms.submodal.com',
		),
		'cookie_domain' => array(
			// These values will each be replaced with the base_url set above.
			'.ms.submodal.com',
		),
	),
	'ee' => array(
		// Connection information for the ExpressionEngine database
		'host' => 'localhost',
		'user' => 'ms',
		'pass' => 'm0d3rn',
		'db' => 'ms_eecms',
		'table_prefix' => 'exp_',
	),
	    // Connection information for the Magento database
	'mage' => array(
		'host' => 'localhost',
		'user' => 'ms',
		'pass' => 'm0d3rn',
		'db' => 'ms_magento',
		'table_prefix' => '',
	),
);
