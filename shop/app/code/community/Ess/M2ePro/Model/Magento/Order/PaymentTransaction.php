<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Magento_Order_PaymentTransaction extends Mage_Core_Model_Abstract
{
    /** @var $magentoOrder Mage_Sales_Model_Order */
    private $magentoOrder = NULL;

    /** @var $transaction Mage_Sales_Model_Order_Payment_Transaction */
    private $transaction = NULL;

    // ########################################

    public function setMagentoOrder(Mage_Sales_Model_Order $magentoOrder)
    {
        $this->magentoOrder = $magentoOrder;
        return $this;
    }

    // ########################################

    public function getPaymentTransaction()
    {
        return $this->transaction;
    }

    // ########################################

    public function buildPaymentTransaction()
    {
        $payment = $this->magentoOrder->getPayment();

        $existTransaction = $payment->getTransaction($this->getData('transaction_id'));

        if ($existTransaction && $existTransaction instanceof Mage_Sales_Model_Order_Payment_Transaction) {
            return NULL;
        }

        $payment->setTransactionId($this->getData('transaction_id'));
        $this->transaction = $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);

        if (defined('Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS')) {
            $this->unsetData('transaction_id');
            $this->transaction
                ->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $this->getData());
        }

        $this->transaction->save();
    }

    // ########################################
}