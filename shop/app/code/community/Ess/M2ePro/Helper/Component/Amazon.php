<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Helper_Component_Amazon extends Mage_Core_Helper_Abstract
{
    const NICK  = 'amazon';
    const TITLE = 'Amazon';

    const MARKETPLACE_US = 29;

    // ########################################

    public function isEnabled()
    {
        return (bool)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/component/'.self::NICK.'/', 'mode');
    }

    public function isAllowed()
    {
        return (bool)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/component/'.self::NICK.'/', 'allowed');
    }

    public function isActive()
    {
        return $this->isEnabled() && $this->isAllowed();
    }

    public function isDefault()
    {
        return Mage::helper('M2ePro/Component')->getDefaultComponent() == self::NICK;
    }

    public function isObject($modelName, $value, $field = NULL)
    {
        $mode = Mage::helper('M2ePro/Component')->getComponentMode($modelName, $value, $field);
        return !is_null($mode) && $mode == self::NICK;
    }

    //-----------------------------------------

    public function getModel($modelName)
    {
        return Mage::helper('M2ePro/Component')->getComponentModel(self::NICK,$modelName);
    }

    public function getObject($modelName, $value, $field = NULL)
    {
        return Mage::helper('M2ePro/Component')->getComponentObject(self::NICK, $modelName, $value, $field);
    }

    public function getCollection($modelName)
    {
        return $this->getModel($modelName)->getCollection();
    }

    // ########################################

    public function getAccount($value, $field = NULL)
    {
        is_null($field) && $field = 'id';

        $cacheKey = self::NICK.'_ACCOUNT_DATA_'.$field.'_'.$value;
        $cacheData = Mage::helper('M2ePro')->getCacheValue($cacheKey);

        if ($cacheData === false) {
            $cacheData = $this->getObject('Account',$value,$field);
            if (is_null($cacheData->getId())) {
                throw new Exception('Such account does not exist!');
            }
            Mage::helper('M2ePro')->setCacheValue($cacheKey,$cacheData,array(self::NICK),60*60*24);
        }

        return $cacheData;
    }

    public function getMarketplace($value, $field = NULL)
    {
        is_null($field) && $field = 'id';

        $cacheKey = self::NICK.'_MARKETPLACE_DATA_'.$field.'_'.$value;
        $cacheData = Mage::helper('M2ePro')->getCacheValue($cacheKey);

        if ($cacheData === false) {
            $cacheData = $this->getObject('Marketplace',$value,$field);
            if (is_null($cacheData->getId())) {
                throw new Exception('Such marketplace does not exist!');
            }
            Mage::helper('M2ePro')->setCacheValue($cacheKey,$cacheData,array(self::NICK),60*60*24);
        }

        return $cacheData;
    }

    // ########################################

    public static function isASIN($string)
    {
        return !empty($string) &&
               $string{0} == 'B' &&
               strlen($string) == 10;
    }

    public static function isISBN($string)
    {
        $string = (string)$string;

        if (strlen($string) == 10) {

            $subTotal = 0;
            $mpBase = 10;
            for ($x=0; $x<=8; $x++) {
                $mp = $mpBase - $x;
                $subTotal += ($mp * $string{$x});
            }

            $rest = $subTotal % 11;
            $checkDigit = $string{9};
            if (strtolower($checkDigit) == "x") {
                $checkDigit = 10;
            }

            return $checkDigit == (11 - $rest);

        } elseif (strlen($string) == 13) {

            $subTotal = 0;
            for ($x=0; $x<=11; $x++) {
                $mp = ($x + 1) % 2 == 0 ? 3 : 1;
                $subTotal += $mp * $string{$x};
            }

            $rest = $subTotal % 10;
            $checkDigit = $string{12};
            if (strtolower($checkDigit) == "x") {
                $checkDigit = 10;
            }

            return $checkDigit == (10 - $rest);
        }

        return false;
    }

    //-----------------------------------------

    public static function isUPC($string)
    {
        return !empty($string) &&
               ctype_digit($string) &&
               (strlen($string) == 8 || strlen($string) == 12);
    }

    public static function isEAN($string)
    {
        return !empty($string) &&
               ctype_digit($string) &&
               (strlen($string) == 8 || strlen($string) == 13);
    }

    // ########################################

    public function getRegisterUrl($marketplaceId = NULL)
    {
        $marketplaceId = (int)$marketplaceId;
        $marketplaceId <= 0 && $marketplaceId = self::MARKETPLACE_US;

        $domain = $this->getMarketplace($marketplaceId)->getUrl();
        $applicationName = Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/amazon/', 'application_name');

        return 'https://sellercentral.'.
                $domain.
                '/gp/mws/registration/register.html?ie=UTF8&*Version*=1&*entries*=0&applicationName='.
                rawurlencode($applicationName).'&appDevMWSAccountId='.
                $this->getMarketplace($marketplaceId)->getDeveloperKey();
    }

    public function getItemUrl($productId, $marketplaceId = NULL)
    {
        $marketplaceId = (int)$marketplaceId;
        $marketplaceId <= 0 && $marketplaceId = self::MARKETPLACE_US;

        $domain = $this->getMarketplace($marketplaceId)->getUrl();

        return 'http://'.$domain.'/gp/product/'.$productId;
    }

    // ########################################

    public function clearAllCache()
    {
        Mage::helper('M2ePro')->removeTagCacheValues(self::NICK);
    }

    public function getCarriers()
    {
        return array(
            'usps'  => 'USPS',
            'ups'   => 'UPS',
            'fedex' => 'FedEx',
            'dhl'   => 'DHL',
            'Fastway',
            'GLS',
            'GO!',
            'Hermes Logistik Gruppe',
            'Royal Mail',
            'Parcelforce',
            'City Link',
            'TNT',
            'Target',
            'SagawaExpress',
            'NipponExpress',
            'YamatoTransport'
        );
    }

    // ########################################
}