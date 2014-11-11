<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Magento FieldType file
 *
 * @package			Magento
 * @version			1.0
 * @author          Nicolas Guerin-Verbialle <nicolas@submodal.com>
 */
 
class Magento_ft extends EE_Fieldtype
{
    /**
	 * Field info (Required)
	 *
	 * @var array
	 * @access public
	 */
	var $info = array(
		'name' 		=> 'Magento',
		'version'	=> '1.0'
	);

    /**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		parent::EE_Fieldtype();

        $this->EE->load->add_package_path(PATH_THIRD . 'magento/');

		$this->EE->lang->loadfile('magento');
		$this->EE->load->library('magento_helper');

		$this->site_id = $this->EE->config->item('site_id');
        if (! defined('MAGENTO_THEME_URL')) define('MAGENTO_THEME_URL', $this->EE->config->item('theme_folder_url') . 'third_party/magento/');
	}

	// --------------------------------------------------------------------

	/**
	 * Display Field on Publish
	 *
	 * @access	public
	 * @param	existing data
	 * @return	field html
	 *
	 */
	public function display_field($data = '')
	{
        $vData = array();
		$vData['mage_field_name'] = $this->field_name;
		$vData['mage_field_id'] = $this->field_id;
        
        // Post DATA?
		if (isset($_POST[$this->field_name])) {
			$data = $_POST[$this->field_name];
		}
		

        $this->EE->magento_helper->mage_meta_parser('css', MAGENTO_THEME_URL . 'mage_field.css', 'mage_field_css');
        $this->EE->magento_helper->mage_meta_parser('js', MAGENTO_THEME_URL . 'mage_field.js', 'mage_field_js');
        $this->EE->magento_helper->mage_meta_parser('js', MAGENTO_THEME_URL . 'jquery.base64.js', 'jquery.base64', 'jquery');
        $this->EE->magento_helper->product_cache_js(); // late-load products
        $this->EE->magento_helper->mage_globals($this->field_id);

        // Add jQuery UI Autocomplete Plugin
		$this->EE->cp->add_js_script(array("ui" => array('core', 'widget', 'position', 'autocomplete')));

        // Grab Assigned Products
		if ($this->EE->input->get_post('entry_id') != FALSE)
		{
			// Grab all products from DB
			$this->EE->db->select('*');
			$this->EE->db->from('magento_products');
			$this->EE->db->where('entry_id', $this->EE->input->get_post('entry_id'));
			$this->EE->db->where('field_id', $vData['mage_field_id']);
			$this->EE->db->order_by('product_order');
			$query = $this->EE->db->get();

			$dbProducts = $query->result();
			$vData['total_products'] = $query->num_rows();
			$query->free_result();
		}
		else
		{
			$dbProducts = array();
		}

        $vData['products'] = $this->EE->magento_helper->getStoredProducts($dbProducts);
        return $this->EE->load->view('mage_field', $vData, TRUE);
    }

	// ********************************************************************************* //

	/**
	 * Validates the field input
	 *
	 * @param $data Contains the submitted field data.
	 * @access public
	 * @return mixed Must return TRUE or an error message
	 */
	public function validate($data)
	{
		// Is this a required field?
		if ($this->settings['field_required'] == 'y')
		{
			if (isset($data['products']) == FALSE OR empty($data['products']) == TRUE)
			{
				return $this->EE->lang->line('magento:required_field');
			}
		}

		return TRUE;
	}

    // ********************************************************************************* //

	/**
	 * Preps the data for saving
	 *
	 * @param $data Contains the submitted field data.
	 * @return string Data to be saved
	 */
	public function save($data)
	{
        $this->EE->session->cache['MagentoProducts']['FieldData'][$this->field_id] = $data;

		if (isset($data['products']) == FALSE)
		{
			return '';
		}
		else
		{
			return '@see MagentoProducts Table';
		}
	}

	// ********************************************************************************* //

	/**
	 * Handles any custom logic after an entry is saved.
	 * Called after an entry is added or updated.
	 * Available data is identical to save, but the settings array includes an entry_id.
	 *
	 * @param $data Contains the submitted field data. (Returned by save())
	 * @access public
	 * @return void
	 */
	public function post_save($data)
	{

		if (isset($this->EE->session->cache['MagentoProducts']['FieldData'][$this->field_id]) == FALSE) return;

		$data = $this->EE->session->cache['MagentoProducts']['FieldData'][$this->field_id];
		$entry_id = $this->settings['entry_id'];
		$clone_id = array_key_exists('clone', $_GET) ? $this->EE->input->get('entry_id') : false;
		$channel_id = $this->EE->input->post('channel_id');
		$field_id = $this->field_id;

        $this->EE->db->set('field_id_' . $this->field_id, $entry_id)->where('entry_id', $entry_id)->update('channel_data');

		// Grab all the files from the DB
		$this->EE->db->select('*');
		$this->EE->db->from('magento_products');
		$this->EE->db->where('entry_id', $clone_id ? $clone_id : $entry_id);
		$this->EE->db->where('field_id', $field_id);
		$query = $this->EE->db->get();

		if (isset($data['products']) == FALSE OR is_array($data['products']) == FALSE)
		{
			$data['products'] = array();
		}

		if ($query->num_rows() > 0)
		{
			// Not fresh, lets see whats new.
			if ($clone_id)
			{
				foreach($query->result_array() as $row)
				{
					$row['entry_id'] = $entry_id;
					unset($row['product_id']);
					$this->EE->db->insert('magento_products', $row);
				}
			}
			foreach ($data['products'] as $order => $product)
			{
				if (isset($product['product_id']) == FALSE)
				{
					$product = base64_decode($product['data']);
					$product = json_decode($product);

					// New Product
					$data = array(	'site_id'	=>	$this->site_id,
									'entry_id'	=>	$entry_id,
									'channel_id'=>	$channel_id,
									'field_id'	=>	$field_id,
									'entity_id'	=>	$product->id,
									'product_order'	=>	$order,
                                    'sku'       => $product->sku,
								);

					$this->EE->db->insert('magento_products', $data);
				}
				else
				{
					// Check for duplicate Products!
					if (isset($product['product_id']) != FALSE)
					{
						// Update Product
						$data = array(
                            'product_order'	=>	$order,
							);

                        $this->EE->db->where('product_id', $product['product_id']);
						$this->EE->db->update('magento_products', $data);
					}
				}
			}
		}
		else
		{
			foreach ($data['products'] as $order => $product)
			{
				$product = base64_decode($product['data']);
				$product = json_decode($product);
				// New Product
                $data = array(	'site_id'	=>	$this->site_id,
                                'entry_id'	=>	$entry_id,
                                'channel_id'=>	$channel_id,
                                'field_id'	=>	$field_id,
                                'entity_id'	=>	$product->id,
                                'product_order'	=>	$order,
                                'sku'       => $product->sku,
                            );

				$this->EE->db->insert('magento_products', $data);
			}
		}

		$this->EE->db->where('entry_id', $entry_id);
		$this->EE->db->where('field_id', $field_id);
		if ( $this->EE->db->count_all_results('magento_products')==0)
		{ // If no products stored, lets clean things up so that {if products} works.
			$this->EE->db->set("field_id_{$field_id}",'')->where('entry_id', $entry_id)->update('channel_data');	
		}

		return;
	}

	// ********************************************************************************* //

	/**
	 * Replace tag
	 *
	 * @access	public
	 * @param	field contents
	 * @return	replacement html
	 *
	 * This is called on the website itself when the field is {rendered}
	 *
	 */
    public function replace_tag($data, $params = array(), $tagdata = FALSE)
    {
        // We define a default Magento block type
        $vData['block'] = (isset($params['block'])) ? $params['block'] : 'eeprod';
        $template = (isset($params['template'])) ? $params['template'] : 'list';
        $this->EE->db->select('sku');
        $this->EE->db->where('entry_id', $data);
        if (array_key_exists('limit', $params))
        {
        	$this->EE->db->limit( $params['limit'] );
        }
        $query = $this->EE->db->get('magento_products');

        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $vData['products'][] = $row->sku;
            }
            return ($this->EE->load->view('template/' . $template, $vData, TRUE));
        }

        return '';

    }

}
