<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Magento Extension
 *
 * @package			Magento
 * @version			1.0
 * @author			Nicolas Guerin-Verbialle <nicolas@submodal.com>
 */
 
class Magento_ext 
{

    /**
	* Extension settings
	*
	* @var	array
	*/
	var $settings = array();

    /**
	* Extension name
	*
	* @var	string
	*/
    var $name               = 'Mage Cookie';

    /**
	* Extension version
	*
	* @var	string
	*/
    var $version            = '1.0';

    /**
	* Extension description
	*
	* @var	string
	*/
    var $description        = 'Logs user in EE when they are already logged in Magento';

    /**
	* Do settings exist?
	*
	* @var	bool
	*/
    var $settings_exist     = 'n';

    /**
	* Documentation link
	*
	* @var	string
	*/
    var $docs_url           = '';

    function __construct($settings ='')
    {
        $this->EE =& get_instance();
        $this->settings = $settings;
    }

    function activate_extension()
    {

        $data = array(
            'class'         => __CLASS__,
            'method'        => 'autologinMember',
            'hook'          => 'sessions_end',
            'settings'      => serialize($this->settings),
            'priority'      => 10,
            'version'       => $this->version,
            'enabled'       => 'y'
        );

        $this->EE->db->insert('extensions', $data);
    }

    function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version)
        {
            return false;
        }

        if ($current < '1.0')
        {
            // Update to version 1.0
        }

        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->update('extensions', array('version' => $this->version));
    }

    function disable_extension()
    {
        $this->EE->db->where('class', __CLASS__);
        $this->EE->db->delete('extensions');
    }

    function autologinMember($obj)
    {
        // If user is logged out from EE
        if ($obj->sdata['session_id'] == 0) {
            if ($this->EE->input->cookie('mage_login_cookie')) {
                $this->EE->load->library('magento_helper');
                $this->EE->magento_helper->bootstrapMage();

                //load cookies prior to session start
                Mage::getSingleton('core/cookie')->get();

                //start core session handler
                Mage::getSingleton('core/session', array('name' => 'frontend'))->start();

                //get customer info
                /* @var $customer Mage_Customer_Model_Session */
                $customerSession = Mage::getSingleton('customer/session');

                if ($customerSession->isLoggedIn()) {

                    $customerEmail = $customerSession->getCustomer()->getOrigData('email');
                    
                    $this->EE->db->select('member_id, group_id, unique_id, password, username');
                    $this->EE->db->where('email', $customerEmail);
                    $query = $this->EE->db->get('members');
                    $row = $query->first_row();
		    if ( $row ) {

                    /** ----------------------------------------
                    /**  Set expiration
                    /** ----------------------------------------*/
                    $expire = 60 * 60 * 24;

                    /** ----------------------------------------
                    /**  Set cookies
                    /** ----------------------------------------*/

                    $this->EE->functions->set_cookie($obj->c_expire, time() + $expire, $expire);
                    $this->EE->functions->set_cookie($obj->c_uniqueid, $row->unique_id, $expire);
                    $this->EE->functions->set_cookie($obj->c_password, $row->password, $expire);
                    $this->EE->functions->set_cookie($obj->c_anon, 1, $expire);

                    /** ----------------------------------------
                    /**  Create a new session
                    /** ----------------------------------------*/
                    
                    $obj->create_new_session($row->member_id, TRUE);
                    $obj->userdata['username'] = $row->username;
                    $obj->userdata['group_id'] = $row->group_id;
		    } else {
			// @TODO: Handle logged into magento but member does not exists in EE here
		    }
                }
            }
        }

    }

    
}
