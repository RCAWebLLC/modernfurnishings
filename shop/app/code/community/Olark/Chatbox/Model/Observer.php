<?php
/**
 * Widget that adds Olark Live Chat to Magento stores.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@olark.com so we can send you a copy immediately.
 *
 * @category    Olark
 * @package     Olark_Chatbox
 * @copyright   Copyright 2012. Habla, Inc. (http://www.olark.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

// See ../etc/config.xml for observer configuration.
// List of events: http://www.nicksays.co.uk/magento-events-cheat-sheet-1-7/
class Olark_Chatbox_Model_Observer
{

    public function recordEvent($observer) {

        // Get a list of the events
        $session = Mage::getSingleton('core/session');
        $events = $session->getData('olark_chatbox_events');

        // Append to this list. Add a timestamp so we know the
        // relative times that the events happened.
        $events[] = array(
            'type' => $observer->getEvent()->getName(),
            'timestamp' => time()
        );

        $session->setData('olark_chatbox_events', $events);

    }

}
