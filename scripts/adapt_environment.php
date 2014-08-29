<?php

class Adapter {
	
	public $connections;
	public $config;
	public $ee_prefix;
	public $mage_prefix;
	
	public function __construct() {
		ob_start();
		ini_set('memory_limit', -1);
		chdir(dirname(__FILE__));		
		
		if ( ! file_exists('config.php')) {
			self::log('Please create a config.php file at '.getcwd()."/config.php. You can start with config.php.tpl file and change it to fit your environment.");
			exit;
		}
		
		$this->config = include("config.php");
		$this->config['docroot'] = dirname(getcwd());
		chdir(dirname(__FILE__));
		
		$this->ee_prefix = isset($this->config['ee']['table_prefix']) ? $this->config['ee']['table_prefix'] : '';
		$this->connections = array();
		$this->connections['ee'] = (object)$this->config['ee'];
		$this->connections['ee']->link = mysql_connect($this->connections['ee']->host, $this->connections['ee']->user, $this->connections['ee']->pass, true);
		if (!$this->connections['ee']->link) die('Could not connect: ' . mysql_error());
		
		$this->mage_prefix = isset($this->config['mage']['table_prefix']) ? $this->config['mage']['table_prefix'] : '';
		$this->connections['mage'] = (object)$this->config['mage'];
		$this->connections['mage']->link = mysql_connect($this->connections['mage']->host, $this->connections['mage']->user, $this->connections['mage']->pass, true);
		if (!$this->connections['mage']->link) die('Could not connect: ' . mysql_error());
	}
	
	public function run() {
		$this->cleanEEUploadPrefs();
		$this->cleanEEChannelURL();
		$this->cleanEESites();
		//$this->cleanEEMeta();
		$this->cleanMageCoreConfig();
		//$this->cleanMageMenuAdmin();
		self::log('Finished adapting data.');
	}
	
	public function cleanEEUploadPrefs() {
		self::log("Adapt EE {$this->ee_prefix}upload_prefs...");
		$rslt = $this->query("SELECT id, server_path, url FROM {$this->ee_prefix}upload_prefs", 'ee');
		while($record = mysql_fetch_object($rslt)) {
			$record->server_path = $this->applyReplacements($record->server_path);
			$record->url = $this->applyReplacements($record->url);
			$this->query("UPDATE {$this->ee_prefix}upload_prefs SET server_path = '$record->server_path', url = '$record->url' WHERE id = $record->id", 'ee');
		}
	}
	public function cleanEEChannelURL() {
		self::log("Adapt EE {$this->ee_prefix}channels.channel_url...");
		$rslt = $this->query("SELECT channel_id, channel_url FROM {$this->ee_prefix}channels", 'ee');
		while($record = mysql_fetch_object($rslt)) {
			$record->channel_url = $this->applyReplacements($record->channel_url);
			$this->query("UPDATE {$this->ee_prefix}channels SET channel_url = '$record->channel_url' WHERE channel_id = $record->channel_id", 'ee');
		}
	}
	
	public function cleanEESites() {
		self::log("Adapt EE {$this->ee_prefix}sites...");
		$rslt = $this->query("
			SELECT
				site_id, site_system_preferences, site_mailinglist_preferences, site_member_preferences,
				site_template_preferences, site_channel_preferences, site_bootstrap_checksums
			FROM {$this->ee_prefix}sites",
			'ee'
		);
		
		while($record = mysql_fetch_object($rslt)) {			
			foreach ($record as $k=>$v) {
				if ($k == 'site_id') continue;
				if (empty($v)) continue;
				$v = unserialize(base64_decode($v));
				foreach ($v as $vk=>&$vv) {
					$vv = $this->applyReplacements($vv);
				}
				$record->{$k} =  base64_encode(serialize($v));
			}
			
			$this->query("
				UPDATE {$this->ee_prefix}sites SET
					site_system_preferences = '$record->site_system_preferences',
					site_mailinglist_preferences = '$record->site_mailinglist_preferences', site_member_preferences = '$record->site_member_preferences',
					site_template_preferences = '$record->site_template_preferences', site_channel_preferences = '$record->site_channel_preferences',
					site_bootstrap_checksums = '$record->site_bootstrap_checksums'
				WHERE site_id = $record->site_id",
				'ee'
			);
		}
	}
	
	public function cleanEEMeta()
	{
		self::log('Adapt EE exp_nsm_better_meta');
		$result = $this->query("SELECT id,canonical_url FROM exp_nsm_better_meta", 'ee');
		while($record = mysql_fetch_object($result)) {
			$url = $this->applyReplacements($record->canonical_url);
			$this->query("UPDATE exp_nsm_better_meta SET canonical_url = '{$url}' WHERE id='{$record->id}'",'ee');
		}
	}
	
	public function cleanMageCoreConfig() {
		self::log("Adapt Mage {$this->mage_prefix}core_config_data...");
		
		$rslt = $this->query("SELECT config_id, value FROM {$this->mage_prefix}core_config_data", 'mage');
		while($record = mysql_fetch_object($rslt)) {			
			$record->value = $this->applyReplacements($record->value);
			$this->query("UPDATE {$this->mage_prefix}core_config_data SET value='$record->value' WHERE config_id = $record->config_id", 'mage');
		}
	}
	
	public function cleanMageMenuAdmin() {
		self::log("Adapt Mage {$this->mage_prefix}menuadmin...");
		
		$rslt = $this->query("SELECT menuadmin_id, link FROM {$this->mage_prefix}menuadmin", 'mage');
		while($record = mysql_fetch_object($rslt)) {			
			$record->link = $this->applyReplacements($record->link);
			$this->query("UPDATE {$this->mage_prefix}menuadmin SET link='$record->link' WHERE menuadmin_id = $record->menuadmin_id", 'mage');
		}
	}
	
	public function applyReplacements($str) {
		foreach ($this->config['replacements']['docroot'] as $r) {
			$str = str_replace($r, $this->config['docroot'], $str);
		}
		
		foreach ($this->config['replacements']['base_url'] as $r) {
			$str = str_replace($r, $this->config['base_url'], $str);
		}

		foreach ($this->config['replacements']['cookie_domain'] as $r) {
			$str = str_replace($r, $this->config['cookie_domain'], $str);
		}
		
		return $str;
	}
	
	public static function log($msg) {
		echo $msg."\n";
		ob_flush();
	}
	
	public function query($sql, $db) {
		$db_selected = mysql_selectdb($this->connections[$db]->db, $this->connections[$db]->link);
		if (!$db_selected) {
			die ('Can\'t select db : ' . mysql_error());
		}
		
		$rslt = mysql_query($sql, $this->connections[$db]->link);
		if (!$rslt) {
			die('Invalid query: ' . mysql_error(). "\n".substr($sql, 0, 150));
		}
		
		return $rslt;
	}
}

$adapter = new Adapter();
$adapter->run();
