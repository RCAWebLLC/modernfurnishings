<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Magento Module Class
 *
 * @package			Magento
 * @version			1.0
 * @author			Nicolas Guerin-Verbialle <nicolas@submodal.com>
 */
 
class Magento
{

    /**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		// Creat EE Instance
		$this->EE =& get_instance();

		$this->site_id = $this->EE->config->item('site_id');

		$this->EE->load->library('magento_helper');

		return;
	}

	// ********************************************************************************* //

	public function products()
	{
        $number = $this->EE->TMPL->fetch_param('number', 1);

        $simpleCollection = $this->EE->magento_helper->getSimpleProducts();
        for($i =0; $i <  $number; $i++) {
            $variables[] = $simpleCollection[$i];
        }

        return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);

    }

    public function magento_router()
	{

		// -----------------------------------------
		// Ajax Request?
		// -----------------------------------------
		$method = $this->EE->input->get_post('ajax_method');
		if( $method=='products' OR (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
		{
			// Load Library
			if (class_exists('Magento_AJAX') != TRUE) include 'ajax.magento.php';

			$AJAX = new Magento_AJAX();

			// Shoot the requested method
			echo $AJAX->$method();
			exit();
		}
	}
    public function family_block()
    {
        $productSku = $this->EE->TMPL->fetch_param('sku');
        if ($productSku) {
            $params = array('product_sku' => $productSku);
            return $this->EE->magento_helper->getBlock('submodal_catalog/product_view_family', $params)->toHtml();
        }
        return '';
    }
    public function block()
    {
        $block = $this->EE->TMPL->fetch_param('block', '');
		$ttl = false;
		if ($this->EE->input->get_request_header('X-varnish-esi'))
		{
			$ttl = (int)$this->EE->TMPL->fetch_param('ttl', '0');
			if ( ! $ttl and in_array($block, array('head','header')))
			{
				$ttl = 1800;
			}
		}
		if ($ttl)
		{
		    $cache = $this->EE->magento_helper->cache_block($block, null, $ttl);
		
		    if ($cache)
		    {
			return $cache;
		    }
		}
	
		$layout_block = $this->EE->magento_helper->getLayoutBlock($block);

		if ( ! $layout_block ) return;

		$data = $layout_block->toHtml();
		
		if ($ttl) $this->EE->magento_helper->cache_block( $block, $data, $ttl );
		
		return $data;
    }

}
