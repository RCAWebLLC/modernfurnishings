<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Observer_Category
{
    //####################################

    public function catalogCategoryChangeProducts(Varien_Event_Observer $observer)
    {
        try {

            // Get category data
            //---------------------------
            $categoryId = $observer->getData('category')->getId();
            $storeId = $observer->getData('category')->getData('store_id');
            //---------------------------

            // Get changes into categories
            //---------------------------
            $changedProductsIds = $observer->getData('product_ids');

            if (count($changedProductsIds) == 0) {
                return;
            }

            $tempArray = $observer->getData('category')->getData('posted_products');
            $postedProductsIds = array();
            foreach ($tempArray as $key => $value) {
                $postedProductsIds[] = $key;
            }

            $addedProducts = array();
            $deletedProducts = array();

            foreach ($changedProductsIds as $productId) {

                if (in_array($productId,$postedProductsIds)) {
                    $addedProducts[] = $productId;
                } else {
                    $deletedProducts[] = $productId;
                }
            }

            if (count($addedProducts) == 0 && count($deletedProducts) == 0) {
                return;
            }
            //---------------------------

            // Make changes with listings
            self::synchChangesWithListings($categoryId,$storeId,$addedProducts,$deletedProducts);

        } catch (Exception $exception) {

            Mage::helper('M2ePro/Exception')->process($exception,true);
            return;
        }
    }

    //-----------------------------------

    public static function synchChangesWithListings($categoryId,
                                                    $storeId,
                                                    $addedProducts,
                                                    $deletedProducts)
    {
        try {

            // Check listings categories
            //---------------------------
            $listingsCategories = Mage::getModel('M2ePro/Listing_Category')
                                            ->getCollection()
                                            ->addFieldToFilter('category_id', $categoryId)
                                            ->toArray();
            if ($listingsCategories['totalRecords'] == 0) {
                return;
            }
            //---------------------------

            // Get all related listings
            //---------------------------
            $listingsIds = array();
            foreach ($listingsCategories['items'] as $listingCategory) {
                $listingsIds[] = $listingCategory['listing_id'];
            }
            $listingsIds = array_unique($listingsIds);

            if (count($listingsIds) == 0) {
                return;
            }

            $listingsModels = array();
            foreach ($listingsIds as $listingId) {
                $tempModel = Mage::getModel('M2ePro/Listing')->loadInstance($listingId);
                /** @var $tempModel Ess_M2ePro_Model_Listing */
                if (!$tempModel->isSourceCategories()) {
                    continue;
                }
                if (Mage::helper('M2ePro/Magento')->isMultiStoreMode() &&
                    $tempModel->getStoreId() != $storeId) {
                    continue;
                }
                $listingsModels[] = $tempModel;
            }

            if (count($listingsModels) == 0) {
                return;
            }
            //---------------------------

            // Add new products
            //---------------------------
            foreach ($addedProducts as $product) {

                $productId = $product instanceof Mage_Catalog_Model_Product ?
                                    (int)$product->getId() : (int)$product;

                foreach ($listingsModels as $listingModel) {

                    /** @var $listingModel Ess_M2ePro_Model_Listing */

                    // Cancel when auto add none set
                    //------------------------------
                    if ($listingModel->getData('categories_add_action') ==
                        Ess_M2ePro_Model_Listing::CATEGORIES_ADD_ACTION_NONE) {
                        continue;
                    }
                    //------------------------------

                    // Only add product
                    //------------------------------
                    if ($listingModel->getData('categories_add_action') ==
                        Ess_M2ePro_Model_Listing::CATEGORIES_ADD_ACTION_ADD) {
                        $listingModel->addProduct(
                            $product instanceof Mage_Catalog_Model_Product ? $product : $productId
                        );
                    }
                    //------------------------------

                    // Add product and list
                    //------------------------------
                    if ($listingModel->getData('categories_add_action') ==
                        Ess_M2ePro_Model_Listing::CATEGORIES_ADD_ACTION_ADD_LIST) {

                        /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */
                        $listingProduct = $listingModel->addProduct(
                            $product instanceof Mage_Catalog_Model_Product ? $product : $productId
                        );

                        if (!($listingProduct instanceof Ess_M2ePro_Model_Listing_Product)) {
                            $tempListingsProducts = $listingModel->getProducts(true,array('product_id'=>$productId));
                            count($tempListingsProducts) > 0 && $listingProduct = array_shift($tempListingsProducts);
                        }

                        if ($listingProduct instanceof Ess_M2ePro_Model_Listing_Product) {
                            $paramsTemp = array();
                            $paramsTemp['status_changer'] = Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_OBSERVER;
                            $listingProduct->isListable() && $listingProduct->listAction($paramsTemp);
                        }
                    }
                    //------------------------------
                }
            }
            //---------------------------

            // Delete products
            //---------------------------
            foreach ($deletedProducts as $product) {

                $productId = $product instanceof Mage_Catalog_Model_Product ?
                                    (int)$product->getId() : (int)$product;

                foreach ($listingsModels as $listingModel) {

                    /** @var $listingModel Ess_M2ePro_Model_Listing */

                    // Cancel when auto delete none set
                    //------------------------------
                    if ($listingModel->getData('categories_delete_action') ==
                        Ess_M2ePro_Model_Listing::CATEGORIES_DELETE_ACTION_NONE) {
                        continue;
                    }
                    //------------------------------

                    // Find needed product
                    //------------------------------
                    $listingsProducts = $listingModel->getProducts(true,array('product_id'=>$productId));

                    if (count($listingsProducts) <= 0) {
                        continue;
                    }

                    $listingProduct = array_shift($listingsProducts);

                    if (!($listingProduct instanceof Ess_M2ePro_Model_Listing_Product)) {
                        continue;
                    }
                    //------------------------------

                    // Only stop product
                    //------------------------------
                    if ($listingModel->getData('categories_delete_action') ==
                        Ess_M2ePro_Model_Listing::CATEGORIES_DELETE_ACTION_STOP) {
                        $paramsTemp = array();
                        $paramsTemp['status_changer'] = Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_OBSERVER;
                        $listingProduct->isStoppable() && $listingProduct->stopAction($paramsTemp);
                    }
                    //------------------------------

                    // Stop product on marketplace and remove
                    //------------------------------
                    if ($listingModel->getData('categories_delete_action') ==
                        Ess_M2ePro_Model_Listing::CATEGORIES_DELETE_ACTION_STOP_REMOVE) {
                        $paramsTemp = array();
                        $paramsTemp['remove'] = true;
                        $paramsTemp['status_changer'] = Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_OBSERVER;
                        $listingProduct->stopAction($paramsTemp);
                    }
                    //------------------------------
                }
            }
            //---------------------------

        } catch (Exception $exception) {

            Mage::helper('M2ePro/Exception')->process($exception,true);
            return;
        }
    }

    //####################################
}