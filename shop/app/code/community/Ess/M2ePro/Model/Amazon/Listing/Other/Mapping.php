<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Amazon_Listing_Other_Mapping
{
    /**
     * @var Ess_M2ePro_Model_Marketplace|null
     */
    protected $marketplace = NULL;

    /**
     * @var Ess_M2ePro_Model_Account|null
     */
    protected $account = NULL;

    protected $mappingSettings = NULL;

    // ########################################

    public function initialize(Ess_M2ePro_Model_Marketplace $marketplace = NULL,
                               Ess_M2ePro_Model_Account $account = NULL)
    {
        $this->marketplace = $marketplace;
        $this->account = $account;
    }

    // ########################################

    public function autoMapOtherListingsProducts(array $otherListings)
    {
        $otherListingsFiltered = array();

        foreach ($otherListings as $otherListing) {

            if (!($otherListing instanceof Ess_M2ePro_Model_Listing_Other)) {
                continue;
            }

            /** @var $otherListing Ess_M2ePro_Model_Listing_Other */

            if ($otherListing->getProductId()) {
                continue;
            }

            $otherListingsFiltered[] = $otherListing;
        }

        if (count($otherListingsFiltered) <= 0) {
            return false;
        }

        $accountsMarketplaces = array();

        foreach ($otherListingsFiltered as $otherListing) {

            /** @var $otherListing Ess_M2ePro_Model_Listing_Other */

            $identifier = $otherListing->getAccountId().'_'.$otherListing->getMarketplaceId();

            if (!isset($accountsMarketplaces[$identifier])) {
                $accountsMarketplaces[$identifier] = array();
            }

            $accountsMarketplaces[$identifier][] = $otherListing;
        }

        $result = true;

        foreach ($accountsMarketplaces as $otherListings) {
            foreach ($otherListings as $otherListing) {
                /** @var $otherListing Ess_M2ePro_Model_Listing_Other */
                $temp = $this->autoMapOtherListingProduct($otherListing);
                $temp === false && $result = false;
            }
        }

        return $result;
    }

    public function autoMapOtherListingProduct(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        if ($otherListing->getProductId()) {
            return false;
        }

        $this->setAccountByOtherListingProduct($otherListing);
        $this->setMarketplaceByOtherListingProduct($otherListing);

        if (!$this->getAccount()->getChildObject()->isOtherListingsMappingEnabled()) {
            return false;
        }

        $mappingSettings = $this->getMappingRulesByPriority();

        foreach ($mappingSettings as $type) {

            $magentoProductId = NULL;

            if ($type == 'general_id') {
                $magentoProductId = $this->getGeneralIdMappedMagentoProductId($otherListing);
            }

            if ($type == 'sku') {
                $magentoProductId = $this->getSkuMappedMagentoProductId($otherListing);
            }

            if ($type == 'title') {
                $magentoProductId = $this->getTitleMappedMagentoProductId($otherListing);
            }

            if (is_null($magentoProductId)) {
                continue;
            }

            //TODO temporarily type simple filter
            if (Mage::getModel('M2ePro/Magento_Product')->loadProduct($magentoProductId)->isProductWithVariations()) {
                continue;
            }

            $otherListing->addData(array('product_id'=>$magentoProductId))->save();

            $dataForAdd = array(
                'account_id' => $this->getAccount()->getId(),
                'marketplace_id' => $this->getMarketplace()->getId(),
                'sku' => $otherListing->getChildObject()->getSku(),
                'product_id' => $otherListing->getProductId(),
                'store_id' => $otherListing->getChildObject()->getRelatedStoreId()
            );

            Mage::getModel('M2ePro/Amazon_Item')->setData($dataForAdd)->save();

            $logModel = Mage::getModel('M2ePro/Listing_Other_Log');
            $logModel->setComponentMode(Ess_M2ePro_Helper_Component_Amazon::NICK);
            $logModel->addProductMessage($otherListing->getId(),
                                         Ess_M2ePro_Model_Log_Abstract::INITIATOR_EXTENSION,
                                         NULL,
                                         Ess_M2ePro_Model_Listing_Other_Log::ACTION_MAP_LISTING,
                                         // Parser hack -> Mage::helper('M2ePro')->__('Item was successfully mapped');
                                         'Item was successfully mapped',
                                         Ess_M2ePro_Model_Log_Abstract::TYPE_NOTICE,
                                         Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM);

            return true;
        }

        return false;
    }

    // ########################################

    protected function getMappingRulesByPriority()
    {
        if (!is_null($this->mappingSettings)) {
            return $this->mappingSettings;
        }

        $this->mappingSettings = array();

        foreach ($this->getAccount()->getChildObject()->getOtherListingsMappingSettings() as $key=>$value) {
            if ((int)$value['mode'] == 0) {
                continue;
            }
            for($i=0;$i<10;$i++) {
                if (!isset($this->mappingSettings[(int)$value['priority']+$i])) {
                    $this->mappingSettings[(string)$value['priority']+$i] = (string)$key;
                    break;
                }
            }
        }

        ksort($this->mappingSettings);

        return $this->mappingSettings;
    }

    //-----------------------------------------

    protected function getGeneralIdMappedMagentoProductId(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        if ($this->getAccount()->getChildObject()->isOtherListingsMappingGeneralIdModeCustomAttribute()) {

            $storeId = $otherListing->getChildObject()->getRelatedStoreId();
            $attributeCode = $this->getAccount()->getChildObject()->getOtherListingsMappingGeneralIdAttribute();
            $attributeValue = trim($otherListing->getChildObject()->getGeneralId());

            $productObj = Mage::getModel('catalog/product')->setStoreId($storeId);
            $productObj = $productObj->loadByAttribute($attributeCode, $attributeValue);

            if ($productObj && $productObj->getId()) {
                return $productObj->getId();
            }
        }

        return NULL;
    }

    protected function getSkuMappedMagentoProductId(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        $attributeCode = NULL;

        if ($this->getAccount()->getChildObject()->isOtherListingsMappingSkuModeProductId()) {

            $productId = trim($otherListing->getChildObject()->getSku());

            if (!ctype_digit($productId) || (int)$productId <= 0) {
                return NULL;
            }

            $product = Mage::getModel('catalog/product')->load($productId);

            if ($product->getId()) {
                return $product->getId();
            }

            return NULL;
        }

        if ($this->getAccount()->getChildObject()->isOtherListingsMappingSkuModeDefault()) {
            $attributeCode = 'sku';
        }

        if ($this->getAccount()->getChildObject()->isOtherListingsMappingSkuModeCustomAttribute()) {
            $attributeCode = $this->getAccount()->getChildObject()->getOtherListingsMappingSkuAttribute();
        }

        if (is_null($attributeCode)) {
            return NULL;
        }

        $storeId = $otherListing->getChildObject()->getRelatedStoreId();
        $attributeValue = trim($otherListing->getChildObject()->getSku());

        $productObj = Mage::getModel('catalog/product')->setStoreId($storeId);
        $productObj = $productObj->loadByAttribute($attributeCode, $attributeValue);

        if ($productObj && $productObj->getId()) {
            return $productObj->getId();
        }

        return NULL;
    }

    protected function getTitleMappedMagentoProductId(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        $attributeCode = NULL;

        if ($this->getAccount()->getChildObject()->isOtherListingsMappingTitleModeDefault()) {
            $attributeCode = 'name';
        }

        if ($this->getAccount()->getChildObject()->isOtherListingsMappingTitleModeCustomAttribute()) {
            $attributeCode = $this->getAccount()->getChildObject()->getOtherListingsMappingTitleAttribute();
        }

        if (is_null($attributeCode)) {
            return NULL;
        }

        $storeId = $otherListing->getChildObject()->getRelatedStoreId();
        $attributeValue = trim($otherListing->getChildObject()->getTitle());

        $productObj = Mage::getModel('catalog/product')->setStoreId($storeId);
        $productObj = $productObj->loadByAttribute($attributeCode, $attributeValue);

        if ($productObj && $productObj->getId()) {
            return $productObj->getId();
        }

        $findCount = preg_match('/^.+(\[(.+)\])$/',$attributeValue,$tempMatches);
        if ($findCount > 0 && isset($tempMatches[1])) {
            $attributeValue = trim(str_replace($tempMatches[1],'',$attributeValue));
            $productObj = Mage::getModel('catalog/product')->setStoreId($storeId);
            $productObj = $productObj->loadByAttribute($attributeCode, $attributeValue);
            if ($productObj && $productObj->getId()) {
                return $productObj->getId();
            }
        }

        return NULL;
    }

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Account
     */
    protected function getAccount()
    {
        return $this->account;
    }

    /**
     * @return Ess_M2ePro_Model_Marketplace
     */
    protected function getMarketplace()
    {
        return $this->marketplace;
    }

    //-----------------------------------------

    protected function setAccountByOtherListingProduct(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        if (!is_null($this->account) && $this->account->getId() == $otherListing->getAccountId()) {
            return;
        }

        $this->account = Mage::helper('M2ePro/Component_Amazon')
                            ->getObject('Account',$otherListing->getAccountId());

        $this->mappingSettings = NULL;
    }

    protected function setMarketplaceByOtherListingProduct(Ess_M2ePro_Model_Listing_Other $otherListing)
    {
        if (!is_null($this->marketplace) && $this->marketplace->getId() == $otherListing->getMarketplaceId()) {
            return;
        }

        $this->marketplace = Mage::helper('M2ePro/Component_Amazon')
                                ->getObject('Marketplace',$otherListing->getMarketplaceId());
    }

    // ########################################
}