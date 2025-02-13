<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class   Ess_M2ePro_Model_Connector_Server_Amazon_Orders_Get_ItemsResponser
extends Ess_M2ePro_Model_Connector_Server_Amazon_Responser
{
    // ########################################

    protected function unsetLocks($fail = false, $message = NULL) {}

    // ########################################

    protected function validateResponseData($response)
    {
        if (!isset($response['items'])) {
            return false;
        }

        return true;
    }

    protected function processResponseData($response)
    {
        $orders = array();

        foreach ($response['items'] as $orderData) {

            $order = array();

            $order['amazon_order_id'] = trim($orderData['id']);
            $order['status'] = trim($orderData['status']);

            $order['marketplace_id'] = trim($orderData['marketplace_id']);
            $order['is_afn_channel'] = (int)$orderData['channel']['is_afn'];

            $order['purchase_create_date'] = $orderData['purchase_date'];
            $order['purchase_update_date'] = $orderData['update_date'];

            $order['buyer_name'] = trim($orderData['buyer']['name']);
            $order['buyer_email'] = trim($orderData['buyer']['email']);

            $order['qty_shipped'] = (int)$orderData['qty']['shipped'];
            $order['qty_unshipped'] = (int)$orderData['qty']['unshipped'];

            $shipping = $orderData['shipping'];

            $order['shipping_service'] = trim($shipping['level']);
            $order['shipping_price'] = isset($orderData['price']['shipping'])
                ? (float)$orderData['price']['shipping'] : 0;

            $order['shipping_address'] = $this->parseShippingAddress($shipping);

            $order['currency'] = isset($orderData['currency'])
                ? trim($orderData['currency']) : '';
            $order['paid_amount'] = isset($orderData['amount_paid'])
                ? (float)$orderData['amount_paid'] : 0;
            $order['tax_amount'] = isset($orderData['price']['tax']['value'])
                ? (float)$orderData['price']['tax']['value'] : 0;
            $order['discount_amount'] = isset($orderData['price']['discount']['value'])
                ? (float)$orderData['price']['discount']['value'] : 0;

            $order['items'] = array();

            foreach ($orderData['items'] as $item) {
                $order['items'][] = array(
                    'amazon_order_item_id' => trim($item['id']),
                    'sku'                  => trim($item['identifiers']['sku']),
                    'general_id'           => trim($item['identifiers']['general_id']),
                    'is_isbn_general_id'   => (int)$item['identifiers']['is_isbn'],
                    'title'                => trim($item['title']),
                    'price'                => (float)$item['prices']['product']['value'],
                    'currency'             => trim($item['prices']['product']['currency']),
                    'tax_amount'           => (float)$item['taxes']['product']['value'],
                    'discount_amount'      => (float)$item['discounts']['total']['value'],
                    'qty_purchased'        => (int)$item['qty']['ordered'],
                    'qty_shipped'          => (int)$item['qty']['shipped']
                );
            }

            $orders[] = $order;
        }

        return $orders;
    }

    private function parseShippingAddress(array $shippingData)
    {
        $location = isset($shippingData['location']) ? $shippingData['location'] : array();
        $address  = isset($shippingData['address']) ? $shippingData['address'] : array();

        return array(
            'county'         => isset($location['county']) ? trim($location['county']) : '',
            'country_code'   => isset($location['country_code']) ? trim($location['country_code']) : '',
            'state'          => isset($location['state']) ? trim($location['state']) : '',
            'city'           => isset($location['city']) ? trim($location['city']) : '',
            'postal_code'    => isset($location['postal_code']) ? $location['postal_code'] : '',
            'recipient_name' => isset($shippingData['buyer']) ? trim($shippingData['buyer']) : '',
            'phone'          => isset($shippingData['phone']) ? $shippingData['phone'] : '',
            'street'         => array(
                isset($address['first']) ? $address['first'] : '',
                isset($address['second']) ? $address['second'] : '',
                isset($address['third']) ? $address['third'] : ''
            )
        );
    }

    // ########################################
}