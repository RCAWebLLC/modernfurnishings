<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Observer_Order
{
    //####################################

    public function salesConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)
    {
        try {

            /* @var $quoteItem Mage_Sales_Model_Quote_Item */
            $quoteItem = $observer->getEvent()->getItem();

            /* @var $product Mage_Catalog_Model_Product */
            $product = $quoteItem->getProduct();

            if (!($product instanceof Mage_Catalog_Model_Product)) {
                return;
            }

            // Get listing where is product
            $listingsArray = Mage::getResourceModel('M2ePro/Listing')->getListingsWhereIsProduct($product->getId());

            if (count($listingsArray) > 0) {

                $qtyOld = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();

                if ($qtyOld <= 0) {
                    return;
                }

                // Save global changes
                //--------------------
                Mage::getModel('M2ePro/ProductChange')
                                ->updateAttribute( $product->getId(),
                                                   'product_instance',
                                                   'any_old',
                                                   'any_new' ,
                                                    Ess_M2ePro_Model_ProductChange::CREATOR_TYPE_OBSERVER );
                //--------------------

                // Save changes for qty
                //--------------------
                $qtyNew = $qtyOld - (int)$quoteItem->getTotalQty();

                $rez = Mage::getModel('M2ePro/ProductChange')
                         ->updateAttribute( $product->getId(),
                                            'qty',
                                            $qtyOld,
                                            $qtyNew,
                                            Ess_M2ePro_Model_ProductChange::CREATOR_TYPE_OBSERVER );

                if ($rez !== false) {

                      foreach ($listingsArray as $listingTemp) {

                             $tempLog = Mage::getModel('M2ePro/Listing_Log');
                             $tempLog->setComponentMode($listingTemp['component_mode']);
                             $tempLog->addProductMessage(
                                $listingTemp['id'],
                                $product->getId(),
                                Ess_M2ePro_Model_Log_Abstract::INITIATOR_EXTENSION,
                                NULL,
                                Ess_M2ePro_Model_Listing_Log::ACTION_CHANGE_PRODUCT_QTY,
                                // Parser hack -> Mage::helper('M2ePro')->__('From [%from%] to [%to%]');
                                Mage::getModel('M2ePro/Log_Abstract')->encodeDescription(
                                    'From [%from%] to [%to%]',array('!from'=>$qtyOld,'!to'=>$qtyNew)
                                ),
                                Ess_M2ePro_Model_Log_Abstract::TYPE_NOTICE,
                                Ess_M2ePro_Model_Log_Abstract::PRIORITY_LOW
                             );
                      }
                }
                //--------------------

                // Save changes for stock Availability
                //--------------------
                $stockAvailabilityOld = (bool)Mage::getModel('cataloginventory/stock_item')
                    ->loadByProduct($product)->getIsInStock();
                $stockAvailabilityNew = !($qtyNew <= (int)Mage::getModel('cataloginventory/stock_item')->getMinQty());

                $rez = Mage::getModel('M2ePro/ProductChange')
                                 ->updateAttribute( $product->getId(),
                                                    'stock_availability',
                                                    (int)$stockAvailabilityOld,
                                                    (int)$stockAvailabilityNew,
                                                    Ess_M2ePro_Model_ProductChange::CREATOR_TYPE_OBSERVER );

                if ($rez !== false) {

                      $stockAvailabilityOld = $stockAvailabilityOld ? 'IN Stock' : 'OUT of Stock';
                      $stockAvailabilityNew = $stockAvailabilityNew ? 'IN Stock' : 'OUT of Stock';

                      foreach ($listingsArray as $listingTemp) {

                             $tempLog = Mage::getModel('M2ePro/Listing_Log');
                             $tempLog->setComponentMode($listingTemp['component_mode']);
                             $tempLog->addProductMessage(
                                $listingTemp['id'],
                                $product->getId(),
                                Ess_M2ePro_Model_Log_Abstract::INITIATOR_EXTENSION,
                                NULL,
                                Ess_M2ePro_Model_Listing_Log::ACTION_CHANGE_PRODUCT_STOCK_AVAILABILITY,
                                // Parser hack -> Mage::helper('M2ePro')->__('From [%from%] to [%to%]');
                                // Parser hack -> Mage::helper('M2ePro')->__('IN Stock');
                                // Parser hack -> Mage::helper('M2ePro')->__('OUT of Stock');
                                Mage::getModel('M2ePro/Log_Abstract')->encodeDescription(
                                    'From [%from%] to [%to%]',
                                    array('from'=>$stockAvailabilityOld,'to'=>$stockAvailabilityNew)
                                ),
                                Ess_M2ePro_Model_Log_Abstract::TYPE_NOTICE,
                                Ess_M2ePro_Model_Log_Abstract::PRIORITY_LOW
                             );
                      }
                }
                //--------------------
            }

        } catch (Exception $exception) {

            Mage::helper('M2ePro/Exception')->process($exception,true);
            return;
        }
    }

    //####################################
}