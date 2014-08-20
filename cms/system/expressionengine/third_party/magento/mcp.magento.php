<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Magento Module: Mcp Class
 * @author Nicolas Guerin-Verbialle <nicolas@submodal.com>
 */
 
class Magento_mcp 
{
    function __construct()
    {
        $this->EE =& get_instance();
	$this->EE->load->library('magento_helper');
    }
    
    function index()
    {
        return $this->EE->load->view('index', array(), true);
    }
    
    function do_purge()
    {
        $this->EE->magento_helper->product_cache(true, false);
        $this->EE->session->set_flashdata('message_success', $this->EE->lang->line('magento:product_cache:purge:success'));
        header('Location: ' . module_url('magento', 'index', false));
        exit();
    }
}
