<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('custom_option_swatch_images')};
CREATE TABLE {$this->getTable('custom_option_swatch_images')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,
  `description` text NULL default '',
  `name` varchar(255) NOT NULL default '',
  `image_base` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('custom_option_swatch_relation')};
CREATE TABLE {$this->getTable('custom_option_swatch_relation')} (
	`swatch_id` int(11)  NOT NULL default '0',
	`product_id` int(11)  NOT NULL default '0',
	`option_id` int(11)  NOT NULL default '0',
	`option_type_id` int(11)  NOT NULL default '0',
  PRIMARY KEY (`swatch_id`, `product_id`, `option_id`, `option_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('custom_option_swatch_log')};
CREATE TABLE {$this->getTable('custom_option_swatch_log')} (
  `log_id` int(11) unsigned NOT NULL auto_increment,
  `num_submited` varchar(255) NOT NULL default '',
  `num_processed` varchar(255) NOT NULL default '',
  `created` datetime NULL,
  `notes` text NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 
