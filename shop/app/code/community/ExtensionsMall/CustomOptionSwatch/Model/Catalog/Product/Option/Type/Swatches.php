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
class ExtensionsMall_CustomOptionSwatch_Model_Catalog_Product_Option_Type_Swatches extends Mage_Catalog_Model_Product_Option_Type_Default {

    public function validateUserValue($values) {
        parent::validateUserValue($values);

        $option = $this->getOption();
        $value = $this->getUserValue();

        if (empty($value) && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
            $this->setIsValid(false);
            Mage::throwException(Mage::helper('catalog')->__('Please specify the product required option(s).'));
        }
        if (!$this->_isSingleSelection()) {
            $valuesCollection = $option->getOptionValuesByOptionId($value, $this->getProduct()->getStoreId())
                    ->load();
            if ($valuesCollection->count() != count($value)) {
                $this->setIsValid(false);
                Mage::throwException(Mage::helper('catalog')->__('Please specify the product required option(s).'));
            }
        }
        return $this;
    }

    public function prepareForCart() {
        if ($this->getIsValid() && $this->getUserValue()) {
            return is_array($this->getUserValue()) ? implode(',', $this->getUserValue()) : $this->getUserValue();
        } else {
            return null;
        }
    }

    public function getFormattedOptionValue($optionValue) {
        if ($this->_formattedOptionValue === null) {
            $this->_formattedOptionValue = Mage::helper('core')->htmlEscape(
                    $this->getEditableOptionValue($optionValue)
            );
        }

        return $this->_formattedOptionValue;
    }

    public function getPrintableOptionValue($optionValue) {
        return $this->getFormattedOptionValue($optionValue);
    }

    protected function _getWrongConfigurationMessage() {
        return Mage::helper('catalog')->__('Some of the products below do not have all the required options. Please edit them and configure all the required options.');
    }

    public function getEditableOptionValue($optionValue) {
        $option = $this->getOption();
        $result = '';
        if (!$this->_isSingleSelection()) {
            foreach (explode(',', $optionValue) as $_value) {
                if ($_result = $option->getValueById($_value)) {
                    $result .= $_result->getTitle() . ', ';
                } else {
                    if ($this->getListener()) {
                        $this->getListener()
                                ->setHasError(true)
                                ->setMessage(
                                        $this->_getWrongConfigurationMessage()
                        );
                        $result = '';
                        break;
                    }
                }
            }
            $result = Mage::helper('core/string')->substr($result, 0, -2);
        } elseif ($this->_isSingleSelection()) {
            if ($_result = $option->getValueById($optionValue)) {
                $result = $_result->getTitle();
            } else {
                if ($this->getListener()) {
                    $this->getListener()
                            ->setHasError(true)
                            ->setMessage(
                                    $this->_getWrongConfigurationMessage()
                    );
                }
                $result = '';
            }
        } else {
            $result = $optionValue;
        }
        return $result;
    }

    public function parseOptionValue($optionValue, $productOptionValues) {
        $_values = array();
        if (!$this->_isSingleSelection()) {
            foreach (explode(',', $optionValue) as $_value) {
                $_value = trim($_value);
                if (array_key_exists($_value, $productOptionValues)) {
                    $_values[] = $productOptionValues[$_value];
                }
            }
        } elseif ($this->_isSingleSelection() && array_key_exists($optionValue, $productOptionValues)) {
            $_values[] = $productOptionValues[$optionValue];
        }
        if (count($_values)) {
            return implode(',', $_values);
        } else {
            return null;
        }
    }

    public function prepareOptionValueForRequest($optionValue) {
        if (!$this->_isSingleSelection()) {
            return explode(',', $optionValue);
        }
        return $optionValue;
    }

    public function getOptionPrice($optionValue, $basePrice) {
        $option = $this->getOption();
        $result = 0;

        if (!$this->_isSingleSelection()) {
            foreach (explode(',', $optionValue) as $value) {
                if ($_result = $option->getValueById($value)) {
                    $result += $this->_getChargableOptionPrice(
                            $_result->getPrice(), $_result->getPriceType() == 'percent', $basePrice
                    );
                } else {
                    if ($this->getListener()) {
                        $this->getListener()
                                ->setHasError(true)
                                ->setMessage(
                                        $this->_getWrongConfigurationMessage()
                        );
                        break;
                    }
                }
            }
        } elseif ($this->_isSingleSelection()) {
            if ($_result = $option->getValueById($optionValue)) {
                $result = $this->_getChargableOptionPrice(
                        $_result->getPrice(), $_result->getPriceType() == 'percent', $basePrice
                );
            } else {
                if ($this->getListener()) {
                    $this->getListener()
                            ->setHasError(true)
                            ->setMessage(
                                    $this->_getWrongConfigurationMessage()
                    );
                }
            }
        }

        return $result;
    }

    public function getOptionSku($optionValue, $skuDelimiter) {
        $option = $this->getOption();

        if (!$this->_isSingleSelection()) {
            $skus = array();
            foreach (explode(',', $optionValue) as $value) {
                if ($optionSku = $option->getValueById($value)) {
                    $skus[] = $optionSku->getSku();
                } else {
                    if ($this->getListener()) {
                        $this->getListener()
                                ->setHasError(true)
                                ->setMessage(
                                        $this->_getWrongConfigurationMessage()
                        );
                        break;
                    }
                }
            }
            $result = implode($skuDelimiter, $skus);
        } elseif ($this->_isSingleSelection()) {
            if ($result = $option->getValueById($optionValue)) {
                return $result->getSku();
            } else {
                if ($this->getListener()) {
                    $this->getListener()
                            ->setHasError(true)
                            ->setMessage(
                                    $this->_getWrongConfigurationMessage()
                    );
                }
                return '';
            }
        } else {
            $result = parent::getOptionSku($optionValue, $skuDelimiter);
        }

        return $result;
    }

    protected function _isSingleSelection() {
        return true;
    }

}
