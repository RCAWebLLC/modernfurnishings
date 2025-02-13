<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Block_Adminhtml_Order extends Ess_M2ePro_Block_Adminhtml_Component_Tabs_Container
{
    public function __construct()
    {
        parent::__construct();

        // Set header text
        //------------------------------
        $this->_headerText = Mage::helper('M2ePro')->__('Orders');
        //------------------------------

        $this->_addButton('feedbacks', array(
            'label'     => Mage::helper('M2ePro')->__('Feedbacks'),
            'onclick'   => 'setLocation(\'' .$this->getUrl('*/adminhtml_ebay_feedback/index').'\')',
            'class'     => 'button_link'
        ));

        $this->_addButton('accounts', array(
            'label'     => Mage::helper('M2ePro')->__('Accounts'),
            'onclick'   => 'setLocation(\'' .$this->getUrl('*/adminhtml_account/index').'\')',
            'class'     => 'button_link'
        ));

        $this->_addButton('reset', array(
            'label'     => Mage::helper('M2ePro')->__('Refresh'),
            'onclick'   => 'CommonHandlerObj.reset_click()',
            'class'     => 'reset'
        ));

        $this->useAjax = true;
    }

    // ########################################

    protected function getHelpBlockJavascript($helpContainerId)
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            return '';
        }

        return <<<JAVASCRIPT
<script type="text/javascript">
    setTimeout(function() {
        ModuleNoticeObj.observeModulePrepareStart($('{$helpContainerId}'));
        OrderHandlerObj.initializeGrids();
    }, 50);
</script>
JAVASCRIPT;
    }

    // ########################################

    protected function getEbayTabBlock()
    {
        if (is_null($this->ebayTabBlock)) {
            $this->ebayTabBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_ebay_order_grid');
        }
        return $this->ebayTabBlock;
    }

    public function getEbayTabHtml()
    {
        $helpBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_ebay_order_help');
        $javascript = $this->getHelpBlockJavascript($helpBlock->getContainerId());

        $filtersBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_account_switcher', '', array(
            'component_mode' => Ess_M2ePro_Helper_Component_Ebay::NICK,
            'controller_name' => 'adminhtml_order'
        ));
        $filtersBlock->setUseConfirm(false);

        return $javascript . $helpBlock->toHtml() . $filtersBlock->toHtml() . parent::getEbayTabHtml();
    }

    protected function getEbayTabUrl()
    {
        return $this->getUrl('*/adminhtml_ebay_order/index');
    }

    // ########################################

    protected function getAmazonTabBlock()
    {
        if (is_null($this->amazonTabBlock)) {
            $this->amazonTabBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_amazon_order_grid');
        }
        return $this->amazonTabBlock;
    }

    public function getAmazonTabHtml()
    {
        $helpBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_amazon_order_help');
        $javascript = $this->getHelpBlockJavascript($helpBlock->getContainerId());

        return $javascript . $helpBlock->toHtml() . $this->getAmazonTabBlockFilterHtml() . parent::getAmazonTabHtml();
    }

    private function getAmazonTabBlockFilterHtml()
    {
        $marketplaceFilterBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_marketplace_switcher', '', array(
            'component_mode' => Ess_M2ePro_Helper_Component_Amazon::NICK,
            'controller_name' => 'adminhtml_order'
        ));
        $marketplaceFilterBlock->setUseConfirm(false);

        $accountFilterBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_account_switcher', '', array(
            'component_mode' => Ess_M2ePro_Helper_Component_Amazon::NICK,
            'controller_name' => 'adminhtml_order'
        ));
        $accountFilterBlock->setUseConfirm(false);

        return '<div class="filter_block">' .
            $marketplaceFilterBlock->toHtml() .
            $accountFilterBlock->toHtml() .
            '</div>';
    }

    protected function getAmazonTabUrl()
    {
        return $this->getUrl('*/adminhtml_amazon_order/index');
    }

    // ########################################

    protected function _componentsToHtml()
    {
        $tempGridIds = array();
        Mage::helper('M2ePro/Component_Ebay')->isActive()   && $tempGridIds[] = $this->getEbayTabBlock()->getId();
        Mage::helper('M2ePro/Component_Amazon')->isActive() && $tempGridIds[] = $this->getAmazonTabBlock()->getId();

        $generalBlock = $this->getLayout()->createBlock('M2ePro/adminhtml_order_general');
        $generalBlock->setGridIds($tempGridIds);

        return $generalBlock->toHtml() . parent::_componentsToHtml();
    }

    // ########################################
}