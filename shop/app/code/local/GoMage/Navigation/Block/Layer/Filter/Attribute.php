<?php
 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2011 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 3.0
 * @since        Class available since Release 1.0
 */

class GoMage_Navigation_Block_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute
{
	
	public function getRemoveUrl($ajax = false)
    {
        $query = array($this->_filter->getRequestVar()=>null);
        $params['_nosid']       = true;
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $query;
        $params['_escape']      = false;        
        
        $params['_query']['ajax'] = null;
        
        if($ajax){
        	
        	$params['_query']['ajax'] = true;
        	
        	
        }
        
        
        return Mage::getUrl('*/*/*', $params);
    }
	
	public function getPopupId(){
		
		return $this->getAttributeModel()->getAttributeCode();
		
	}
	
	public function ajaxEnabled(){

	    if (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch'){
	        $is_ajax = true; 
	    }else{
	        $is_ajax = Mage::registry('current_category') && 
                       Mage::registry('current_category')->getisAnchor() &&
                       (Mage::registry('current_category')->getDisplayMode() != Mage_Catalog_Model_Category::DM_PAGE);
	    }
	    
	    $is_ajax = $is_ajax && ($this->getAttributeModel()->getIsAjax() == 1);
	    		
		return ($is_ajax ? 1 : 0);		
				
	}
	
	public function canShowMinimized(){
		
		if('true' === Mage::app()->getFrontController()->getRequest()->getParam($this->_filter->getRequestVar().'_is_open')){
		
			return false;
		
		}elseif('false' === Mage::app()->getFrontController()->getRequest()->getParam($this->_filter->getRequestVar().'_is_open')){
			
			return true;
			
		}
		
		return (bool) ($this->getAttributeModel()->getShowMinimized() > 0);
		
	}
	
	public function getShowAllOptions(){
		
		if('true' === Mage::app()->getFrontController()->getRequest()->getParam($this->_filter->getRequestVar().'_show_all')){
		
			return true;
		
		}elseif('false' === Mage::app()->getFrontController()->getRequest()->getParam($this->_filter->getRequestVar().'_show_all')){
			
			return false;
			
		}
		
		return false;
		
	}
	
	public function getVisibleOptions(){
		
		return (int) $this->getAttributeModel()->getVisibleOptions();
		
	}
	
	public function canShowPopup(){
		
		return (bool) ($this->getAttributeModel()->getShowHelp() > 0);
		
	}
	
	public function getPopupText(){
		
		return trim($this->getAttributeModel()->getPopupText());
		
	}
	
	public function getPopupWidth(){
		
		return (int) $this->getAttributeModel()->getPopupWidth();
		
	}
	
	public function getPopupHeight(){
		
		return (int) $this->getAttributeModel()->getPopupHeight();
		
	}
	
	public function canShowCheckbox(){
		
		return $this->getAttributeModel()->getShowCheckbox();
		
	}
	
	public function canShowLabels(){
		
		return (bool) $this->getAttributeModel()->getShowImageName();
		
	}
	
	public function getImageWidth(){
		
		return (int) $this->getAttributeModel()->getImageWidth();
		
	}
	
	public function getImageHeight(){
		
		return (int) $this->getAttributeModel()->getImageHeight();
		
	}
	
	public function getImageAlign(){
		
		switch($this->getAttributeModel()->getImageAlign()):
		
			default:
				
				$image_align = 'default';
				
			break;
			
			case (1):
				
				$image_align = 'horizontally';
				
			break;
			
			case (2):
				
				$image_align = '2-columns';
				
			break;
			
		endswitch;
		
		return $image_align;
		
	}
	
    public function canShowResetFirler(){
		
		return (bool) ($this->getAttributeModel()->getFilterReset() > 0);
		
	}
	
	/**
     * Initialize filter template
     *
     */
    
    protected function _prepareFilter(){
    	
    	parent::_prepareFilter();
    	
    	if(Mage::helper('gomage_navigation')->isGomageNavigation()){
        	
        	switch($this->getAttributeModel()->getFilterType()): 
        	
	        	default:
	        	
	        		$this->_template = ('gomage/navigation/layer/filter/default.phtml');
	        	
	        	break;
	        	
	        	case(GoMage_Navigation_Model_Layer::FILTER_TYPE_IMAGE):
	        	
	        		$this->_template = ('gomage/navigation/layer/filter/image.phtml');
	        	
	        	break;
	        	
	        	case(GoMage_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN):
	        	
	        		$this->_template = ('gomage/navigation/layer/filter/dropdown.phtml');
	        	
	        	break;
        	
        	endswitch;
        	
        }
    	
    }
    
    public function getFilterType(){
        return $this->getAttributeModel()->getFilterType();    
    }
    
    public function getInBlockHeight(){
        return $this->getAttributeModel()->getInblockHeight();    
    }
    
}
