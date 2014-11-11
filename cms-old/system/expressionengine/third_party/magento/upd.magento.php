<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Magento Module: Update Class
 * @author Nicolas Guerin-Verbialle <nicolas@submodal.com>
 */
 
class Magento_upd 
{

    /**
	 * Module version
	 *
	 * @var string
	 * @access public
	 */
	public $version		=	'1.0';

	/**
	 * Module Short Name
	 *
	 * @var string
	 * @access private
	 */
	private $module_name	=	'magento';

	/**
	 * Has Control Panel Backend?
	 *
	 * @var string
	 * @access private
	 */
	private $has_cp_backend = 'n';

	/**
	 * Has Publish Fields?
	 *
	 * @var string
	 * @access private
	 */
	private $has_publish_fields = 'n';

    /**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
    function __construct()
    {
        $this->EE =& get_instance();
    }


	// ********************************************************************************* //

	/**
	 * Installs the module
	 *
	 * Installs the module, adding a record to the exp_modules table
	 *
	 * @access public
	 * @return boolean
	 **/
    public function install()
    {
		// Load dbforge
		$this->EE->load->dbforge();

		//----------------------------------------
		// EXP_MODULES
		//----------------------------------------
		$module = array(	'module_name' => ucfirst($this->module_name),
							'module_version' => $this->version,
							'has_cp_backend' => $this->has_cp_backend,
							'has_publish_fields' => $this->has_publish_fields );

		$this->EE->db->insert('modules', $module);

        //----------------------------------------
		// EXP_MAGENTO_PRODUCTS
		//----------------------------------------
		$fields = array(
			'product_id'	 	=> array('type' => 'INT',		'unsigned' => TRUE,	'auto_increment' => TRUE),
			'site_id'		=> array('type' => 'TINYINT',	'unsigned' => TRUE,	'default' => 1),
			'entry_id'		=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'channel_id'	=> array('type' => 'INT',		'unsigned' => TRUE, 'default' => 0),
			'field_id'		=> array('type' => 'MEDIUMINT',	'unsigned' => TRUE, 'default' => 1),
			'entity_id'	    => array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'product_order'	=> array('type' => 'SMALLINT',	'unsigned' => TRUE, 'default' => 1),
		);

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('product_id', TRUE);
		$this->EE->dbforge->add_key('entry_id');
		$this->EE->dbforge->create_table('magento_products', TRUE);

        //----------------------------------------
		// EXP_ACTIONS
		//----------------------------------------
		$module = array(	'class' => ucfirst($this->module_name),
							'method' => $this->module_name . '_router' );

		$this->EE->db->insert('actions', $module);
    }


	// ********************************************************************************* //

	/**
	 * Uninstalls the module
	 *
	 * @access public
	 * @return Boolean FALSE if uninstall failed, TRUE if it was successful
	 **/
	function uninstall()
	{
		// Load dbforge
		$this->EE->load->dbforge();

		// Remove
		$this->EE->dbforge->drop_table('magento_products');

        $this->EE->db->where('module_name', ucfirst($this->module_name));
		$this->EE->db->delete('modules');

        $this->EE->db->where('class', ucfirst($this->module_name));
		$this->EE->db->delete('actions');

		return TRUE;
	}

	// ********************************************************************************* //

	/**
	 * Updates the module
	 *
	 * This function is checked on any visit to the module's control panel,
	 * and compares the current version number in the file to
	 * the recorded version in the database.
	 * This allows you to easily make database or
	 * other changes as new versions of the module come out.
	 *
	 * @access public
	 * @return Boolean FALSE if no update is necessary, TRUE if it is.
	 **/
	public function update($current = '')
	{
		// Are they the same?
		if ($current >= $this->version)
		{
			return FALSE;
		}

		// For Version < 2.0
    	if ($current < '2.0' )
    	{

    	}

		// Upgrade The Module
		$this->EE->db->set('module_version', $this->version);
		$this->EE->db->where('module_name', ucfirst($this->module_name));
		$this->EE->db->update('exp_modules');

		return TRUE;
	}

} // END CLASS

/* End of file upd.magento.php */
/* Location: ./system/expressionengine/third_party/magento/upd.magento.php */


