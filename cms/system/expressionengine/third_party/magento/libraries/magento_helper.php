<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Magento Helper File
 *
 * @package			Magento
 * @version			1.0
 * @author			Nicolas Guerin-Verbialle <nicolas@submodal.com>
 */
 
class Magento_helper 
{

    protected $siteId;

    protected $storeId;

    protected $_mageBootstraped = false;

    protected $_layoutLoaded = false;

    protected $_runcode = 'default';

    /**
	 * Constructor
	 *
	 * @access public
	 */
    public function __construct()
	{
		// Create EE Instance
		$this->EE =& get_instance();
        // @TODO bring that back into CP as an input field
		(!defined('ROOTBASEPATH')) ? define('ROOTBASEPATH', realpath(dirname(__FILE__).'/../../../../../../')) : true;
		//if(isset($_SERVER['EE_RUN_CODE'])) $this->siteId = $_SERVER['EE_RUN_CODE'];
		$this->siteId = $this->EE->config->item('site_id');
	}

    // ********************************************************************************* //

    public function bootstrapMage()
    {
        if (!$this->_mageBootstraped) {
            /* @var string */
            $basePath = ROOTBASEPATH;
            $mageFilename = '/shop/app/Mage.php';

            require_once($basePath . $mageFilename);
            if (!Mage::registry('ee_instance_loading')) {
                /* @var string */
                $this->_runcode = $mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : 'default';
                $mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';
    
                Mage::app($mageRunCode, $mageRunType);
                if (!Mage::registry('ee_instance')) {
                    Mage::register('ee_instance', $this->EE);
                }
                Mage::getSingleton('core/session', array('name' => 'frontend'))->start();
                Mage::app()->loadAreaPart('frontend', 'events');
            }
            // We flag Magento as bootstraped
            $this->_mageBootstraped = true;
        }
    }

        /**
     * Gets all simple products in Magento
     * @TODO, cache them?
     * @return array
     */
    public function getSimpleProducts()
	{

        $this->bootstrapMage();
        $products = array();
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->setStoreId($this->matchSite())
                ->addFieldToFilter('type_id', 'simple')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', array('in' => array(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)));

        foreach($collection as $product) {
            $products[$product->getData('sku')] = array(
                    'name'      => $product->getData('name'),
                    'image'     => (string)Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(110, 80),
                    'id'        => $product->getData('entity_id'),
                );
        }
        return $products;
	}


    /**
     * Get all products from their entity Ids
     * @param array $ids
     * @return array
     */
    public function getStoredProducts(array $dbProducts) {
        $this->bootstrapMage();
        foreach($dbProducts AS &$product) {
            $p = Mage::getModel('catalog/product')->load($product->entity_id);
            $product->name  = $p->getData('name');
            $product->image = (string)Mage::helper('catalog/image')->init($p, 'thumbnail')->resize(110, 80);
            $product->sku   = $p->getData('sku');
        }
        return $dbProducts;
    }

    public function matchSite()
    {
        $this->storeId = $_SERVER['MAGE_RUN_CODE'];
        return $this->storeId;
    }

    public function mage_meta_parser($type='js', $url, $name, $package='')
	{
		// -----------------------------------------
		// CSS
		// -----------------------------------------
		if ($type == 'css')
		{
			if ( isset($this->EE->session->cache['DevDemon']['CSS'][$name]) == FALSE )
			{
				$this->EE->cp->add_to_head('<link rel="stylesheet" href="' . $url . '" type="text/css" media="print, projection, screen" />');
				$this->EE->session->cache['DevDemon']['CSS'][$name] = TRUE;
			}
		}

		// -----------------------------------------
		// Javascript
		// -----------------------------------------
		if ($type == 'js')
		{
			if ( isset($this->EE->session->cache['DevDemon']['JS'][$name]) == FALSE )
			{
				$this->EE->cp->add_to_head('<script src="' . $url . '" type="text/javascript"></script>');
				$this->EE->session->cache['DevDemon']['JS'][$name] = TRUE;
			}
		}

		// -----------------------------------------
		// Global Inline Javascript
		// -----------------------------------------
		/*if ($type == 'gjs')
		{
			if ( isset($this->EE->session->cache['DevDemon']['GJS'][$name]) == FALSE )
			{
				$xid = $this->xid_generator();
				$AJAX_url = $this->get_router_url();

				$js = "	var ChannelVideos = ChannelVideos ? ChannelVideos : new Object();
						ChannelVideos.XID = '{$xid}';
						ChannelVideos.AJAX_URL = '{$AJAX_url}';
						ChannelVideos.siteId = '{$this->siteId}';
					";

				$this->EE->cp->add_to_head('<script type="text/javascript">' . $js . '</script>');
				$this->EE->session->cache['DevDemon']['GJS'][$name] = TRUE;
			}
		}*/
	}


    /**
     * Deprecated
     */
    public function mage_products_to_js()
    {
        $this->product_cache_js(); 
    }

    public function mage_globals($field_id) {
        $AJAX_url = $this->_get_router_url();
        $js = "var Magento = Magento ? Magento : new Object();
						Magento.AJAX_URL = '{$AJAX_url}';
						Magento.fieldID = '{$field_id}';
					";
        $script = '<script type="text/javascript">';
        $script .= $js;
        $script .= '</script>';
        if ( isset($this->EE->session->cache['DevDemon']['GJS']['Magento']) == FALSE )
			{
				$this->EE->cp->add_to_foot($script);
				$this->EE->session->cache['DevDemon']['GJS']['Magento'] = TRUE;
			}
    }

    private function _get_router_url()
    {
        // Do we have a cached version of our ACT_ID?
        if (isset($this->EE->session->cache['Magento']['Router_Url']['ACT_ID']) == FALSE) {
            $this->EE->db->select('action_id');
            $this->EE->db->where('class', 'Magento');
            $this->EE->db->where('method', 'magento_router');
            $query = $this->EE->db->get('actions');
            $ACT_ID = $query->row('action_id');
        }
        else $ACT_ID = $this->EE->session->cache['Magento']['Router_Url']['ACT_ID'];

        // Grab Site URL
        $url = $this->EE->functions->fetch_site_index(0, 0);

        // Check for index.php
        if (substr($url, -9, 9) != 'index.php') {
            $url .= 'index.php';
        }

        // Check for last slash
        //if (substr($url, -1) != '/') $url .= '/';

        if (defined('MASKED_CP') == FALSE OR MASKED_CP == FALSE) {
            // Replace site url domain with current working domain
            $server_host = (isset($_SERVER['HTTP_HOST']) == TRUE && $_SERVER['HTTP_HOST'] != FALSE)
                    ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
            $url = preg_replace('#http\://(([\w][\w\-\.]*)\.)?([\w][\w\-]+)(\.([\w][\w\.]*))?\/#', "http://{$server_host}/", $url);
        }

        // Create new URL
        $ajax_url = $url . QUERY_MARKER . 'ACT=' . $ACT_ID;

        if (isset($this->EE->session->cache['Magento']['Router_Url']['URL']) == TRUE) return $this->EE->session->cache['Magento']['Router_Url']['URL'];
        $this->EE->session->cache['Magento']['Router_Url']['URL'] = $ajax_url;
        return $this->EE->session->cache['Magento']['Router_Url']['URL'];

    }

	public function loadLayout()
	{
	    $this->bootstrapMage();
        if (!$this->_layoutLoaded) {
            $layout = Mage::app()->getLayout();
            $layout->getUpdate()->addHandle('default');
            $layout->getUpdate()->load();
            $layout->generateXml();
            $layout->generateBlocks();	    
            $this->_layoutLoaded = true;
	    }
	}
	/**
	 * loads expression engine magento layout and returns block if it exists
	 * $this->EE->magento_helper->getLayoutBlock('header')->toHtml();
	 * @param str $name
	 * @return Mage_Core_Block_Template
	 */
	public function getLayoutBlock($name)
	{
	    $this->bootstrapMage();
        $this->loadLayout();
        return Mage::app()->getLayout()->getBlock($name);
	}
	/**
	 * create magento block
	 * $this->EE->magento_helper->
	 *   getBlock('catalog/product',
	 *       array(
	 *          'product_sku'=>'iphone-yellow',
	 *          'template'=>'catalog/product/view/slider.phtml'
	 *          )
	 *       )
	 *   ->toHtml();
	 * @param str $type
	 * @param array $attributes
	 */
	public function getBlock($type, array $attributes=null)
	{
	    $this->bootstrapMage();
        $layout = Mage::app()->getLayout();
	    $block = $layout->createBlock($type, '',  $attributes);
	    return $block;
	}
	/**
     *  base64_encode() for URLs encoding
     *
     *  @param    string $url
     *  @return	  string
     */
    public function encodeUrl($url)
    {
        return strtr(base64_encode($url), '+/=', '-_,');
    }
    
    function product_cache($purge = false, $serve = true)
    {
	$ttl = 3600;
	$expire_time = time() - $ttl;
	$cachefile = APPPATH . 'cache/magento_products.json';
	
	if ( $purge or file_exists($cachefile)===FALSE or (filemtime($cachefile) < $expire_time) )
	{
	    $products = $this->getSimpleProducts();
	    $contents = 'var jProducts = ' . json_encode($products);
	    
	    $fh = fopen($cachefile, 'w');
	    fwrite($fh, $contents);
	    fclose($fh);
	}
	
	// serve cache
	
	if ( $serve and ( isset($contents) or file_exists($cachefile)) )
	{
	    if ( ! isset( $contents) )
	    {
		$contents = file_get_contents($cachefile);
	    }
	    
	    return $contents;
	    
	}
    }
    
    function product_cache_url()
    {
	$base = $this->_get_router_url();
	return $base . '&ajax_method=products';
    }
    
    function product_cache_js()
    {
        if ( isset($this->EE->session->cache['DevDemon']['GJS']['jProducts']) == FALSE )
	{
	    $src_url = $this->product_cache_url();
	    $script = "<script type=\"text/javascript\" src=\"{$src_url}\"></script>";
	    $this->EE->cp->add_to_foot($script);
	    $this->EE->session->cache['DevDemon']['GJS']['jProducts'] = TRUE;
	}
    }
    
    
    function cache_block( $block_name, $data = null, $ttl = 900 )
    {
        # Cache per domain name for international sites
	$hash = $_SERVER['SERVER_NAME'] . $this->_runcode . md5( $block_name );
	$path = APPPATH . 'cache/magento/blocks/';
	if ( ! file_exists($path) ) {
	  @mkdir($path, 0777, TRUE);
	}
	$cache = $path.$hash;
	
	if ( $data )
	{
	    file_put_contents($cache, $data);
	    return $data;
	}
	
	if ( file_exists($cache) and time() < (filemtime($cache) + $ttl))
	{
	    return file_get_contents($cache);
	}
	
	return FALSE;
	
	
    }
    
    
    
    
    
    
    
    
}
