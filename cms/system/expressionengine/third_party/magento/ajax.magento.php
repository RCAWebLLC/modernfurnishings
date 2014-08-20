<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Magento Ajax Class
 *
 * @package			Magento
 * @version			1.0
 * @author			Nicolas Guerin-Verbialle <nicolas@submodal.com>
 */
 
class Magento_AJAX 
{

    /**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();

		$this->EE->load->library('magento_helper');
		$this->EE->lang->loadfile('magento');

		if ($this->EE->input->get_post('site_id')) $this->site_id = $this->EE->input->get_post('site_id');
		elseif ($this->EE->input->cookie('cp_last_site_id')) $this->site_id = $this->EE->input->cookie('cp_last_site_id');
		else $this->site_id = $this->EE->config->item('site_id');
	}

	// ********************************************************************************* //

	/**
	 * Get a specific product
	 *
	 * @access public
	 * @return string
	 */
    public function getProductById()
    {
        // Output
		$this->out = array('success' => 'no', 'content' => '');

        $productId = $this->EE->input->get_post('product_id');
        $product = $this->EE->magento_helper->getProductbyId($productId);

        if ($product) {
            $this->out['content'] = $product;
		    $this->out['success'] = 'yes';
        }

        exit(json_encode($this->out));
    }

    // ********************************************************************************* //

	/**
	 * Delete reference to a specific product from a content piece
	 *
	 * @access public
	 * @void
	 */
    public function deleteProduct()
    {
        // Check for PRODUCT_ID
		if ($this->EE->input->post('product_id') == FALSE OR is_numeric($this->EE->input->post('product_id')) == FALSE)
		{
			echo "NO PRODUCT ID: \n" . print_r($_POST, TRUE);
			exit();
		}

		$this->EE->db->where('product_id', $this->EE->input->post('product_id'));
		$this->EE->db->delete('magento_products');

		exit('DONE');
    }
    
    function products()
    {
	echo $this->EE->magento_helper->product_cache();
	exit();
    }
    
}
