<?php

class Bongo_Checkout_Model_Adminhtml_RegionsUs
{
    protected $_countries;
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $countriesArray = Mage::getResourceModel('directory/country_collection')->load()
                ->toOptionArray(false);
            $this->_countries = array();
            foreach ($countriesArray as $a) {
                $this->_countries[$a['value']] = $a['label'];
            }

            $countryRegions = array();
            $regionsCollection = Mage::getResourceModel('directory/region_collection')->addCountryFilter('US')->load();
            foreach ($regionsCollection as $region) {
                $countryRegions[$region->getCountryId()][$region->getId()] = $region->getDefaultName();
            }
            uksort($countryRegions, array($this, 'sortRegionCountries'));

            $this->_options = array();
            foreach ($countryRegions as $countryId=>$regions) {
                $regionOptions = array();
                foreach ($regions as $regionId=>$regionName) {
                    $regionOptions[] = array('label'=>$regionName, 'value'=>$regionId);
                }
                $this->_options[] = array('label'=>$this->_countries[$countryId], 'value'=>$regionOptions);
            }
        }
        $options = $this->_options;

        return $options;
    }

    public function sortRegionCountries($a, $b)
    {
        return strcmp($this->_countries[$a], $this->_countries[$b]);
    }
}
