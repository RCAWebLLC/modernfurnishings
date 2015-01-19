<?php

class Bongo_Checkout_CallbackController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		try {
			/*//debug start
			$_POST ['status'] = 'N';
			$_POST ['partner_key'] = 'bc5f0e66fa87f6134ea594c53a6baaf7';
			$_POST ['order'] = 'PHJzcyB2ZXJzaW9uPSIyLjAiPg0KICAgIDxjaGFubmVsPg0KICAgICAgICA8dGl0bGU+Qm9uZ28gUGF5IHwgIFBhcnRuZXIgfCBPcmRlciB0byB2ZXJpZnk8L3RpdGxlPg0KICAgICAgICA8bGluaz48L2xpbms+DQogICAgICAgIDxkZXNjcmlwdGlvbj5PcmRlciB0byB2ZXJpZnk8L2Rlc2NyaXB0aW9uPg0KICAgICAgICA8bGFuZ3VhZ2U+ZW4tZW48L2xhbmd1YWdlPg0KICAgICAgICA8Y29weXJpZ2h0PmJvbmdvdXMuY29tPC9jb3B5cmlnaHQ+DQogICAgICAgIDxpdGVtPg0KICAgICAgICAgICAgPGlkb3JkZXI+ODMxMzk8L2lkb3JkZXI+DQogICAgICAgICAgICA8dHJhY2tpbmdsaW5rPmh0dHBzOi8vYm9uZ291cy5jb20vdHJhY2tpbmcvUzU5OTUtWTIyTTk2NDE5TjUwQTgyTTwvdHJhY2tpbmdsaW5rPg0KICAgICAgICAgICAgPGN1c3RvbWVyZmlyc3RuYW1lPjwhW0NEQVRBW3NhbV1dPjwvY3VzdG9tZXJmaXJzdG5hbWU+DQogICAgICAgICAgICA8Y3VzdG9tZXJsYXN0bmFtZT48IVtDREFUQVtkZSBzaWx2YV1dPjwvY3VzdG9tZXJsYXN0bmFtZT4NCiAgICAgICAgICAgIDxjb21wYW55PjwhW0NEQVRBW11dPjwvY29tcGFueT4NCiAgICAgICAgICAgIDxjdXN0b21lcmFkZHJlczE+PCFbQ0RBVEFbMTIzIHNlYXZpZXcgYXZlXV0+PC9jdXN0b21lcmFkZHJlczE+DQogICAgICAgICAgICA8Y3VzdG9tZXJhZGRyZXMyPjwhW0NEQVRBW11dPjwvY3VzdG9tZXJhZGRyZXMyPg0KICAgICAgICAgICAgPGN1c3RvbWVyY2l0eT48IVtDREFUQVticmlkZ2Vwb3J0XV0+PC9jdXN0b21lcmNpdHk+DQogICAgICAgICAgICA8Y3VzdG9tZXJjb3VudHJ5PjwhW0NEQVRBW1VTXV0+PC9jdXN0b21lcmNvdW50cnk+DQogICAgICAgICAgICA8Y3VzdG9tZXJzdGF0ZT48IVtDREFUQVtDb25uZWN0aWN1dF1dPjwvY3VzdG9tZXJzdGF0ZT4NCiAgICAgICAgICAgIDxjdXN0b21lcnppcD48IVtDREFUQVswNjYwN11dPjwvY3VzdG9tZXJ6aXA+DQogICAgICAgICAgICA8Y3VzdG9tZXJwaG9uZT48IVtDREFUQVsxODEzNTA3NDU0NV1dPjwvY3VzdG9tZXJwaG9uZT4NCiAgICAgICAgICAgIDxjdXN0b21lcmVtYWlsPjwhW0NEQVRBW3NhbS5kZXNpbHZhQGJvbmdvdXMuY29tXV0+PC9jdXN0b21lcmVtYWlsPg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwcm9kdWN0cz4NCiAgICAgICAgICAgICAgICAgICAgPGl0ZW1wcm9kdWN0cz4NCiAgICAgICAgICAgICAgICAgICAgICAgIDxwcm9kdWN0aWQ+PCFbQ0RBVEFbMjExMl1dPjwvcHJvZHVjdGlkPg0KICAgICAgICAgICAgICAgICAgICAgICAgPHF0eT4xPC9xdHk+DQogICAgICAgICAgICAgICAgICAgICAgICA8cHJpY2U+MTA8L3ByaWNlPg0KICAgICAgICAgICAgICAgICAgICAgICAgPGN1c3RvbV8xPjwhW0NEQVRBW11dPjwvY3VzdG9tXzE+DQogICAgICAgICAgICAgICAgICAgICAgICA8Y3VzdG9tXzI+PCFbQ0RBVEFbXV0+PC9jdXN0b21fMj4NCiAgICAgICAgICAgICAgICAgICAgICAgIDxjdXN0b21fMz48IVtDREFUQVtdXT48L2N1c3RvbV8zPg0KICAgICAgICAgICAgICAgICAgICA8L2l0ZW1wcm9kdWN0cz4NCiAgICAgICAgICAgICAgICA8L3Byb2R1Y3RzPg0KICAgICAgICAgICAgICAgICAgICAgICAgPHNoaXBhZGRyZXNzMT48IVtDREFUQVsxMDkwMSBSb29zZXZlbHQgQmx2ZC4gU3RlLiAxMDAwXV0+PC9zaGlwYWRkcmVzczE+DQogICAgICAgICAgICA8c2hpcGFkZHJlc3MyPiMgMTcyNjI5PC9zaGlwYWRkcmVzczI+DQogICAgICAgICAgICA8c2hpcGNpdHk+PCFbQ0RBVEFbU3QuIFBldGVyc2J1cmddXT48L3NoaXBjaXR5Pg0KICAgICAgICAgICAgPHNoaXBzdGF0ZT48IVtDREFUQVtGTF1dPjwvc2hpcHN0YXRlPg0KICAgICAgICAgICAgPHNoaXB6aXA+PCFbQ0RBVEFbMzM3MTZdXT48L3NoaXB6aXA+DQogICAgICAgICAgICA8c2hpcGNvdW50cnk+PCFbQ0RBVEFbVVNdXT48L3NoaXBjb3VudHJ5Pg0KICAgICAgICAgICAgPHNoaXBwaG9uZT48IVtDREFUQVs2NDYuNDkwLjI2OTFdXT48L3NoaXBwaG9uZT4NCiAgICAgICAgICAgIDxvcmRlcnN1YnRvdGFsPjEwPC9vcmRlcnN1YnRvdGFsPg0KICAgICAgICAgICAgPG9yZGVyZHV0eWNvc3Q+MC4wMDwvb3JkZXJkdXR5Y29zdD4NCiAgICAgICAgICAgIDxvcmRlcnRheGNvc3Q+MTQuMDM8L29yZGVydGF4Y29zdD4NCiAgICAgICAgICAgIDxvcmRlcnNoaXBwaW5nY29zdD41My42Mjwvb3JkZXJzaGlwcGluZ2Nvc3Q+DQogICAgICAgICAgICA8b3JkZXJzaGlwcGluZ2Nvc3Rkb21lc3RpYz4wPC9vcmRlcnNoaXBwaW5nY29zdGRvbWVzdGljPg0KICAgICAgICAgICAgPG9yZGVyaW5zdXJhbmNlY29zdD4xLjI3PC9vcmRlcmluc3VyYW5jZWNvc3Q+DQogICAgICAgICAgICA8b3JkZXJjdXJyZW5jeWNvZGU+PC9vcmRlcmN1cnJlbmN5Y29kZT4NCiAgICAgICAgICAgIDxvcmRlcnRvdGFsPjc4LjkyPC9vcmRlcnRvdGFsPg0KICAgICAgICAgICAgPGN1c3RvbV9vcmRlcjE+PCFbQ0RBVEFbMTddXT48L2N1c3RvbV9vcmRlcjE+DQogICAgICAgICAgICA8Y3VzdG9tX29yZGVyMj48IVtDREFUQVtdXT48L2N1c3RvbV9vcmRlcjI+DQogICAgICAgICAgICA8Y3VzdG9tX29yZGVyMz48IVtDREFUQVtdXT48L2N1c3RvbV9vcmRlcjM+DQogICAgICAgICAgICA8b3JkZXJsYW5kZWRDb3N0VHJhbnNhY3Rpb25JRD4wZDlkNDk1ZWRmNWQwYjdkNzYwMDI5YTFkZTgzNjFjOTwvb3JkZXJsYW5kZWRDb3N0VHJhbnNhY3Rpb25JRD4NCiAgICAgICAgICAgIDxwdWJEYXRlPjIwMTMtMTEtMjUgMTQ6MDU6MDM8L3B1YkRhdGU+DQogICAgICAgIDwvaXRlbT4NCiAgICA8L2NoYW5uZWw+DQo8L3Jzcz4=';
			//debug end*/
			

			if (empty ( $_POST ['status'] ) || empty ( $_POST ['partner_key'] ) || empty ( $_POST ['order'] )) {
				throw new Exception ( 'Invalid request' );
			}
			
			if (! in_array ( $_POST ['status'], array ('P', 'C', 'N', 'V', 'B' ) )) {
				throw new Exception ( 'Invalid status' );
			}
			
			if ($_POST ['partner_key'] !== Mage::getStoreConfig ( 'bongocheckout_config/config/api_key' )) {
				throw new Exception ( 'Invalid partner key' );
			}
			
			if (! function_exists ( 'simplexml_load_string' )) {
				throw new Exception ( 'PHP extension SimpleXML not installed' );
			}
			
			$xml = simplexml_load_string ( base64_decode ( $_POST ['order'] ) );
			
			if (! $xml) {
				throw new Exception ( 'Invalid or malformed order request' );
			}
			
			$orders = array ($xml->channel->item );
			
			foreach ( $orders as $order ) {
				$collection = Mage::getModel ( 'sales/order' )->getCollection ()->addAttributeToSelect ( 'entity_id' )->addAttributeToFilter ( 'bongo_id', ( int ) $order->idorder )->load ();
				
				$order_id = null;
				
				if ($collection->count () > 0) {
					foreach ( $collection as $existing ) {
						$order_id = ( int ) $existing->getId ();
					}
				}
				
				if (empty ( $order_id )) {
					$order_items = array ();
					
					foreach ( $order->products as $product ) {
						$order_items [( string ) $product->itemproducts->productid] = ( int ) $product->itemproducts->qty;
					}
					
					$quote = Mage::getModel ( 'sales/quote' )->load ( ( int ) $order->custom_order1 );
					$quote->setIsActive ( true )->save ();
					$quote->reserveOrderId ();
					
					$billing = $quote->getBillingAddress ();
					$billing->setFirstname ( ( string ) $order->customerfirstname );
					$billing->setLastname ( ( string ) $order->customerlastname );
					$billing->setCompany ( ( string ) $order->company );
					
					$billing_street = array (( string ) $order->customeraddres1 );
					
					if (( string ) $order->customeraddres2 != "") {
						$street [] = ( string ) $order->customeraddres2;
					}
					
					$billing->setStreet ( $billing_street );
					$billing->setCity ( ( string ) $order->customercity );
					$billing->setRegion ( ( string ) $order->customerstate );
					$billing->setRegionId ( '' );
					$billing->setPostcode ( ( string ) $order->customerzip );
					$billing->setCountryId ( ( string ) $order->customercountry );
					$billing->setTelephone ( ( string ) $order->customerphone );
					$billing->setEmail ( ( string ) $order->customeremail );
					
					$shipping = $quote->getShippingAddress ();
					$shipping->setFirstname ( ( string ) $order->customerfirstname );
					$shipping->setLastname ( ( string ) $order->customerlastname );
					$shipping->setCompany ( ( string ) $order->company );
					
					$shipping_street = array (( string ) $order->shipaddress1 );
					
					if (( string ) $order->shipaddress2 != "") {
						$street [] = ( string ) $order->shipaddress2;
					}
					
					$shipping->setStreet ( $shipping_street );
					$shipping->setCity ( ( string ) $order->shipcity );
					$shipping->setRegion ( ( string ) $order->shipstate );
					$shipping->setRegionId ( '' );
					$shipping->setPostcode ( ( string ) $order->shipzip );
					$shipping->setCountryId ( ( string ) $order->shipcountry );
					$shipping->setTelephone ( ( string ) $order->shipphone );
					$shipping->setSameAsBilling ( false );
					$shipping->setShippingMethod ( 'bongocheckout_economy' );
					$quote->setShippingMethod ( 'bongocheckout_economy' );
					$quote->getPayment ()->importData ( array ('method' => 'bongocheckout' ) );
					$quote->setPaymentMethod ( 'bongocheckout' );
					$quote->save ();
					
					$convert = Mage::getModel ( 'sales/convert_quote' );
					$new_order = $convert->toOrder ( $quote );
					//$new_order->addressToOrder ( $quote->getShippingAddress(), $new_order );
					$new_order->setBillingAddress ( $convert->addressToOrderAddress ( $billing ) );
					$new_order->setShippingAddress ( $convert->addressToOrderAddress ( $shipping ) );
					$new_order->setPayment ( $convert->paymentToOrderPayment ( $quote->getPayment () ) );
					$new_order->setSendConfirmation ( false );
					
					foreach ( $quote->getAllItems () as $item ) {
						$product = $item->getProduct ();
						
						if ($product->getTypeId () !== 'configurable' && $product->getTypeId () !== 'bundle' && $product->getTypeId () !== 'grouped' && ! in_array ( ( string ) $item->getSku (), array_keys ( $order_items ) )) {
							continue;
						}
						
						$item->setQty ( $order_items [( string ) $item->getSku ()] );
						
						$orderItem = $convert->itemToOrderItem ( $item );
						
						$options = array ();
						if ($productOptions = $product->getTypeInstance ( true )->getOrderOptions ( $product )) {
							
							$options = $productOptions;
						}
						if ($addOptions = $item->getOptionByCode ( 'additional_options' )) {
							$options ['additional_options'] = unserialize ( $addOptions->getValue () );
						}
						if ($options) {
							$orderItem->setProductOptions ( $options );
						}
						if ($item->getParentItem ()) {
							$orderItem->setParentItem ( $new_order->getItemByQuoteItemId ( $item->getParentItem ()->getId () ) );
						}
						$new_order->addItem ( $orderItem );
					}
					
					$shippingtotal = (float) $order->orderdutycost + (float) $order->ordertaxcost + (float) $order->ordershippingcost + (float) $order->ordershippingcostdomestic + (float) $order->orderinsurancecost;
					$total = (float) $order->ordersubtotal + $shippingtotal;
					$new_order->setShippingMethod ( 'bongocheckout_economy' );
					$new_order->setShippingDescription ( Mage::getStoreConfig ( 'carriers/bongocheckout/title' ) . ' - ' . Mage::getStoreConfig ( 'carriers/bongocheckout/name' ) );
					$new_order->setSubtotal ( (float) $order->ordersubtotal );
					$new_order->setBaseSubtotal ( (float) $order->ordersubtotal );
					$new_order->setShippingAmount ( $shippingtotal );
					$new_order->setBaseShippingAmount ( $shippingtotal );
					$new_order->setGrandTotal ( $total );
					$new_order->setBaseGrandTotal ( $total );
					
					$new_order->addStatusToHistory ( 'pending', 'Imported order from Bongo' )->setIsCustomerNotified ( false );
					$new_order->place ();
					$new_order->save ();
					
					$quote->setIsActive ( false )->save ();
					
					$order_id = ( int ) $new_order->getId ();
				}
				
				if (empty ( $order_id )) {
					throw new Exception ( 'Unable to create order' );
				}
				
				$update_order = Mage::getModel ( 'sales/order' )->load ( ( int ) $order_id );
				$update_order->setBongoId ( ( int ) $order->idorder );
				$update_order->setBongoTracking ( ( string ) $order->trackinglink );
				$update_order->setBongoStatusCode ( $_POST ['status'] );
				$update_order->setBongoStatusDate ( ( string ) $order->pubDate );
				$update_order->setBongoLandedcosttransactionid ( ( string ) $order->orderlandedCostTransactionID );
				$update_order->setBongoTotalsSubtotal ( ( string ) $order->ordersubtotal );
				$update_order->setBongoTotalsDuty ( ( string ) $order->orderdutycost );
				$update_order->setBongoTotalsTax ( ( string ) $order->ordertaxcost );
				$update_order->setBongoTotalsShipping ( ( string ) $order->ordershippingcost );
				$update_order->setBongoTotalsDomestic ( ( string ) $order->ordershippingcostdomestic );
				$update_order->setBongoTotalsInsurance ( ( string ) $order->orderinsurancecost );
				$update_order->setBongoTotalsTotal ( ( string ) $order->ordertotal );
				$update_order->save ();
				
				if ($_POST ['status'] == 'C' && $update_order->canCancel ()) {
					$update_order->addStatusToHistory ( Mage_Sales_Model_Order::STATE_CANCELED, 'Order canceled by Bongo' )->setIsCustomerNotified ( false );
					$update_order->cancel ()->save ();
				} else if ($_POST ['status'] == 'B' && $update_order->canCancel ()) {
					$update_order->addStatusToHistory ( Mage_Sales_Model_Order::STATE_CANCELED, 'Order blacklisted by Bongo' )->setIsCustomerNotified ( false );
					$update_order->cancel ()->save ();
				} else if ($_POST ['status'] == 'V' && $update_order->canInvoice ()) {
					$invoice = Mage::getModel ( 'sales/service_order', $update_order )->prepareInvoice ();
					
					if (! $invoice->getTotalQty ()) {
						throw new Exception ( 'Order is missing products' );
					}
					
					$invoice->setRequestedCaptureCase ( Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE );
					$invoice->register ();
					$transactionSave = Mage::getModel ( 'core/resource_transaction' )->addObject ( $invoice )->addObject ( $invoice->getOrder () );
					$transactionSave->save ();
					$update_order->addStatusToHistory ( Mage_Sales_Model_Order::STATE_PROCESSING, 'Payment processed by Bongo' )->setIsCustomerNotified ( false );
					$update_order->cancel ()->save ();
				} else if ($_POST ['status'] == 'P' && $update_order->getStatus () !== 'pending') {
					$update_order->addStatusToHistory ( 'pending', 'Marked as Pending by Bongo' )->setIsCustomerNotified ( false );
					$update_order->save ();
				} else if ($_POST ['status'] == 'N' && $update_order->getStatus () !== 'pending') {
					$update_order->addStatusToHistory ( 'pending', 'Marked as New by Bongo' )->setIsCustomerNotified ( false );
					$update_order->save ();
				}
				
				echo '{SUCCESS}';
			}
		} catch ( Exception $e ) {
			Mage::log ( "Callback Error: {$e->getMessage()}; Timestamp: " . date ( "Y-m-d H:i:s", Mage::getModel ( 'core/date' )->timestamp ( time () ) ) . "; Raw Request: " . print_r ( $_POST, true ), null, 'bongo_exception.log' );
			
			echo $e->getMessage ();
		}
	}
}