<?php

/**
 * ExtensionsMall
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ExtensionsMall EULA that is available through 
 * the world-wide-web at this URL: http://www.extensionsmall.com/license.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to support@extensionsmall.com for more information.
 *
 * @category   ExtensionsMall
 * @package    ExtensionsMall_CustomOptionSwatch
 * @author     ExtensionsMall Dev Team
 * @copyright  Copyright (c) 2015 ExtensionsMall (http://www.extensionsmall.com/)
 * @license    http://www.extensionsmall.com/license.html
 */
class ExtensionsMall_CustomOptionSwatch_Helper_Image extends Mage_Core_Helper_Abstract {

    protected $swatchId = null;
    protected $swatchBaseImageName = null;
    protected $baseImageURL = null;
    protected $baseImagePath = null;
    protected $imageUrl = null;
    protected $imagePath = null;
    protected $placeholderImage = '/images/catalog/product/placeholder/image.jpg';
    protected $constrainOnly = true;
    protected $keepAspectRatio = false;
    protected $keepFrame = false;
    protected $keepTransparency = true;

    public function init($swatch, $baseImage = null) {

        if ($swatch instanceof ExtensionsMall_CustomOptionSwatch_Model_Swatches) {
            $this->swatchId = $swatch->getId();
            $this->swatchBaseImageName = $swatch->getImageBase();
        } else if ($swatch instanceof ExtensionsMall_CustomOptionSwatch_Model_Catalog_Product_Option_Value) {
            $this->swatchId = $swatch->getSwatchId();
            $this->swatchBaseImageName = $swatch->getSwatchImageBase();
        } else if (is_numeric($swatch) && $swatch > 0) {
            $this->swatchId = $swatch;
            $this->swatchBaseImageName = $baseImage;

            if ($this->swatchBaseImageName == null) {
                $object = Mage::getModel('custom_option_swatch/swatches')->load($swatch);
                if ($object->getId() > 0) {
                    $this->swatchBaseImageName = $object->getImageBase();
                } else {
                    $this->swatchId = null;
                }
            }
        }

        if (is_null($this->swatchId)) {
            $this->swatchBaseImageName = $this->placeholderImage;
            return $this;
        }

        $this->baseImagePath = Mage::getBaseDir('media') . DS . 'custom_option_swatch' . DS . $this->swatchId . DS . $this->swatchBaseImageName;
        $this->baseImageURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'custom_option_swatch' . DS . $this->swatchId . DS . $this->swatchBaseImageName;

        return $this;
    }

    public function isImageExsist() {
        return (is_file($this->baseImagePath) && file_exists($this->baseImagePath) ? true : false);
    }

    public function __toString() {
        return ($this->imageUrl) ? $this->imageUrl : $this->baseImageURL;
    }

    function constrainOnly($data = true) {
        $this->constrainOnly = $data;
        return $this;
    }

    function keepAspectRatio($data = true) {
        $this->keepAspectRatio = $data;
        return $this;
    }

    function keepFrame($data = true) {
        $this->keepFrame = $data;
        return $this;
    }

    function keepTransparency($data = true) {
        $this->keepTransparency = $data;
        return $this;
    }

    public function resize($width = null, $height = null) {

        if ($width == NULL && $height == NULL) {
            $width = Mage::getStoreConfig('custom_option_swatch/general/swatch_width');
            $height = Mage::getStoreConfig('custom_option_swatch/general/swatch_height');
        }

        $resizePath = $width . 'x' . $height;
        $resizePathFull = Mage::getBaseDir('media') . DS . 'custom_option_swatch' . DS . $this->swatchId . DS . $resizePath . DS . $this->swatchBaseImageName;

        if (file_exists($this->baseImagePath) && is_file($this->baseImagePath) && !file_exists($resizePathFull)) {

            $imageObj = new Varien_Image($this->baseImagePath);
            $imageObj->constrainOnly($this->constrainOnly);
            $imageObj->keepAspectRatio($this->keepAspectRatio);
            $imageObj->keepFrame($this->keepFrame);
            $imageObj->keepTransparency($this->keepTransparency);

            $imageObj->resize($width, $height);
            $imageObj->save($resizePathFull);
        }

        $this->imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "custom_option_swatch/{$this->swatchId}/{$resizePath}/{$this->swatchBaseImageName}";
        $this->imagePath = $resizePathFull;

        return $this;
    }

}
