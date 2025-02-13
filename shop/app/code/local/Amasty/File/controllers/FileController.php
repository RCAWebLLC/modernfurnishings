<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_File
 */
class Amasty_File_FileController extends Mage_Core_Controller_Front_Action
{
    public function downloadAction()
    {
        $fileId = $this->getRequest()->getParam('file_id');

        Mage::helper('amfile')->giveFile($fileId);
    }

    public function testApiAction()
    {
        $api = Mage::getModel('amfile/api');

        var_dump($api->getAttachments(889,0, 58));

    }
}
