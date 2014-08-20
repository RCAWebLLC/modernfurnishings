<?php

/**
 * |___  /                   | |     | |    |___  /
 *    / / _ __ ___   ___   __| |_   _| | ___   / /   ___ ___  _ __ ___
 *   / / | '_ ` _ \ / _ \ / _` | | | | |/ _ \ / /   / __/ _ \| '_ ` _ \
 *  / /__| | | | | | (_) | (_| | |_| | |  __// /__ | (_| (_) | | | | | |
 * /_____|_| |_| |_|\___/ \__,_|\__,_|_|\___/_____(_)___\___/|_| |_| |_|
 *
 *
 * Ups Freight Shipping Extension
 *
 * @category   ZmoduleZ
 * @package    ZmoduleZ_UpsFreightModule
 * @copyright  Copyright (c) 2009 ZmoduleZ (http://www.zmodulez.com)
 * @license    http://www.zmodulez.com/license/license.txt
 * @author     sales@zmodulez.com>
 * @version    1.6.0
 */
abstract class ZmoduleZ_UpsFreightModule_Model_Carrier_Abstractupsfreight extends Mage_Usa_Model_Shipping_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

	protected $_request = null;
	protected $_result = null;
	protected $_xmlAccessRequest = null;

	abstract protected function _getOpco();
	abstract protected function _getCarrierCode();

	public function _buldProdXmlRequest(){
		$r = $this->_rawRequest;
		$user = $this->getConfigData('token_username');
		$pass = Mage::helper('core')->decrypt($this->getConfigData('token_password'));
		$key = Mage::helper('core')->decrypt($this->getConfigData('key'));
		$securityHeader = $this->buildSecurity($user,$pass,$key);

		$itemWeight = intval($r->getWeight())+""+$this->getConfigData('palletweight');
		$shipFrom = $this->buildAddress('', '', '', '', $r->getOrigPostal(), $r->getOrigCountry());
		$shipTo = $this->buildAddress('', '', '', '', $r->getDestPostal(), $r->getDestCountry());
		$payer = $this->buildAddress('Payer', $this->getConfigData('billto_address'), $this->getConfigData('billto_city'), $this->getConfigData('billto_state'), $this->getConfigData('billto_zip'), $this->getConfigData('billto_country'));
		$shipmentBillingOptionCode = $this->getConfigData('requestor_type');
		$shipmentBillingOptionDesc = $this->getConfigData('requestor_type');
		$freightClass = $this->getConfigData('freight_class');

		return $this->_constructXMLRequest($securityHeader, $shipFrom, $shipTo, $payer, $shipmentBillingOptionCode, $shipmentBillingOptionDesc, $itemWeight, $freightClass);
	}

	public function _buldTestXmlRequest(){
		$r = $this->_rawRequest;
		$user = $this->getConfigData('token_username');
		$pass = Mage::helper('core')->decrypt($this->getConfigData('token_password'));
		$key = Mage::helper('core')->decrypt($this->getConfigData('key'));
		$securityHeader = $this->buildSecurity($user,$pass,$key);

		$itemWeight = '1500';
		$shipFrom = $this->buildAddress('Developer Test 1', '101 Developer Way', 'Richmond', 'VA', '23224', 'US');
		$shipTo = $this->buildAddress('Consignee Test 1', '1000 Consignee Street', 'Allanton', 'MO', '63001', 'US');
		$payer = $this->buildAddress('Developer Test 1', '101 Developer Way', 'Richmond', 'VA', '23224', 'US');
		$shipmentBillingOptionCode = '10';
		$shipmentBillingOptionDesc = 'PREPAID';
		$freightClass = '92.5';

		return $this->_constructXMLRequest($securityHeader, $shipFrom, $shipTo, $payer, $shipmentBillingOptionCode, $shipmentBillingOptionDesc, $itemWeight, $freightClass);
	}

	public function _constructXMLRequest($securityHeader, $shipFrom, $shipTo, $payer, $shipmentBillingOptionCode, $shipmentBillingOptionDesc, $itemWeight, $freightClass){
		return '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
			xmlns:q0="http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0" xmlns:q1="http://www.ups.com/XMLSchema/XOLTWS/FreightRate/v1.0"
			xmlns:q2="http://www.ups.com/XMLSchema/XOLTWS/Common/v1.0" xmlns:xsd="http://www.w3.org/2001/XMLSchema"
			xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
			<soapenv:Header>
				'.$securityHeader.'
			</soapenv:Header>
			<soapenv:Body>
				<q1:FreightRateRequest>
					<q2:Request />
					<q1:ShipFrom>
						'.$shipFrom.'
					</q1:ShipFrom>
					<q1:ShipTo>
						'.$shipTo.'
					</q1:ShipTo>
					<q1:PaymentInformation>
						<q1:Payer>
							'.$payer.'
						</q1:Payer>
						<q1:ShipmentBillingOption>
							<q1:Code>'.$shipmentBillingOptionCode.'</q1:Code>
							<q1:Description>'.$shipmentBillingOptionDesc.'</q1:Description>
						</q1:ShipmentBillingOption>
					</q1:PaymentInformation>
					<q1:Service>
						<q1:Code>308</q1:Code>
						<q1:Description>UPS Freight LTL</q1:Description>
					</q1:Service>
					<q1:HandlingUnitOne>
						<q1:Quantity>1</q1:Quantity>
						<q1:Type>
							<q1:Code>PLT</q1:Code>
							<q1:Description>PALLET</q1:Description>
						</q1:Type>
					</q1:HandlingUnitOne>
					<q1:Commodity>
						<q1:Description>item</q1:Description>
						<q1:Weight>
							<q1:Value>'.$itemWeight.'</q1:Value>
							<q1:UnitOfMeasurement>
								<q1:Code>LBS</q1:Code>
								<q1:Description>Pounds</q1:Description>
							</q1:UnitOfMeasurement>
						</q1:Weight>
						<q1:NumberOfPieces>1</q1:NumberOfPieces>
						<q1:PackagingType>
							<q1:Code>PLT</q1:Code>
						</q1:PackagingType>
						<q1:FreightClass>'.$freightClass.'</q1:FreightClass>
					</q1:Commodity>
				</q1:FreightRateRequest>
			</soapenv:Body>
		</soapenv:Envelope>';

	}

	public function _buildContext($xmlRequest){
		$header = <<<HEAD
Content-Type: text/xml; charset=utf-8
SOAPAction: 'http://onlinetools.ups.com/webservices/FreightRateBinding'
HEAD;

		$paramAry = Array(
			'http' => Array(
				'method' => "POST",
				'header' => $header,
				'content' => $xmlRequest ));

		return stream_context_create($paramAry);
	}

	public function _callService($xmlRequest){

		if ( $this->getConfigFlag('testMode')){
			$gateway_url = $this->getConfigData('test_gateway_url');
		}
		else{
			$gateway_url = $this->getConfigData('prod_gateway_url');
		}


		$fp = @fopen($gateway_url, 'rb', false, $this->_buildContext($xmlRequest));
		$serviceResponse = @stream_get_contents($fp);

		return $serviceResponse;
	}

	public function _getQuotes($showResidentialRate) {
		Mage::log("getting UPS Freight LTL quotes ...");

		// if fixed rate is used, don't call the xml service.  Use handling fees to obtain rates
		if ($this->getConfigFlag('usefixedrate')){
			$expectedArrivalDate = strftime("%a, %m/%d/%Y", mktime(0, 0, 0, date("m")  , date("d")+$this->getConfigData('adddaystodeliverydate'), date("Y")));
			$result = Mage::getModel('shipping/rate_result');
			$result->append($this->buildRate($expectedArrivalDate, 0, $this->getConfigData('methodtext'), 0, 0));
			return $result;
		}

		$isTestMode = $this->getConfigFlag('testMode');
		if ($isTestMode) {
			$xmlRequest = $this->_buldTestXmlRequest();
		}else{
			$xmlRequest = $this->_buldProdXmlRequest();
		}
		//Mage::log('UPS Freight request : '.$xmlRequest);
		$responseBody = $this->_callService($xmlRequest);

		return $this->_parseXmlResponse($xmlRequest, $responseBody, $showResidentialRate, $isTestMode);
	}

	public function buildAddress($name, $addressLine, $city, $stateProvinceCode, $postalCode, $countryCode){
		return '
	        <q1:Name>'.$name.'</q1:Name>
	        <q1:Address>
	          <q1:AddressLine>'.$addressLine.'</q1:AddressLine>
	          <q1:City>'.$city.'</q1:City>
	          <q1:StateProvinceCode>'.$stateProvinceCode.'</q1:StateProvinceCode>
	          <q1:PostalCode>'.$postalCode.'</q1:PostalCode>
	          <q1:CountryCode>'.$countryCode.'</q1:CountryCode>
	        </q1:Address>';
	}

	public function buildSecurity($userName, $password, $accessLicenseNumber){
		return  '
			<q0:UPSSecurity>
				<q0:UsernameToken>
					<q0:Username>'.$userName.'</q0:Username>
					<q0:Password>'.$password.'</q0:Password>
				</q0:UsernameToken>
				<q0:ServiceAccessToken>
					<q0:AccessLicenseNumber>'.$accessLicenseNumber.'</q0:AccessLicenseNumber>
				</q0:ServiceAccessToken>
			</q0:UPSSecurity>';
	}

	public function _parseXmlResponse($xmlRequest, $response, $showResidentialRate, $isTestMode) {
		//Mage::log('RESPONSE: '.$response);
		$response = str_replace(':', '_', $response);
		$netFreightCharge = null;
		$xml = null;
		if (!is_null($response)) {
			$xml = simplexml_load_string($response);

			if (isset($response) && !is_null($xml) && $xml !== '' && is_null($xml->soapenv_Body->soapenv_Fault->faultcode)){
				if ($isTestMode){
					$rateObj = $xml->soapenv_Body->freightRate_FreightRateResponse->freightRate_TotalShipmentCharge;
					$netFreightCharge =  floatval($rateObj->freightRate_MonetaryValue);
				}else{
					$rateObj = $xml->soapenv_Body->freightRate_FreightRateResponse->freightRate_Rate->freightRate_Factor;
					$netFreightCharge =  floatval($rateObj->freightRate_Value);
				}
				//Mage::log("The returned amount is : ".$netFreightCharge);
			}
			else {
				Mage::log("Unable to load xml from response.");
				Mage::log('response='.$response);
			}
		}
		else {
			Mage::log("XML Response was null.");
		}

		$result = Mage::getModel('shipping/rate_result');
		if (!isset($netFreightCharge) || is_null($netFreightCharge) || $netFreightCharge == "") {
			Mage::log("Rate Request error occurred. See request/response below:");
			Mage::log('XML Request'.$xmlRequest);
			Mage::log('XML Response'.$response);

			// don't show the error twice
			if (!$showResidentialRate){

				$error = Mage::getModel('shipping/rate_result_error');
				$error->setCarrier('ups');
				$error->setCarrierTitle($this->getConfigData('title'));
				//$error->setErrorMessage($errorTitle);
				$error->setErrorMessage($this->getConfigData('specificerrmsg'));
				$result->append($error);

				$error = Mage::getModel('shipping/rate_result_error');
				$error->setCarrier('upsfreight');
				$error->setCarrierTitle($this->getConfigData('title'));
				$error->setErrorMessage($this->getConfigData('specificerrmsg'));
				$result->append($error);
			}
		}
		else {
			//$transitDays = intval($xml->{'transit-days'});
			//$expectedArrivalDate = strftime("%a, %m/%d/%Y", mktime(0, 0, 0, date("m")  , date("d")+$transitDays+$this->getConfigData('adddaystodeliverydate'), date("Y")));

			// if weight exceeds max freight cost, adjust down to configured max freight cost
			$maxCost = $this->getConfigData('max_freight_cost');
			if ($netFreightCharge > $maxCost) {
				$netFreightCharge = $maxCost;
			}

			if ($isTestMode){
				$result->append($this->buildRate(0, $netFreightCharge, 'TEST MODE - Use this value when requesting your UPS Freight production access key.  You can change the mode within the shipping method\'s configuration', 0, 0));
			}
			else{
				if ($showResidentialRate){
					$result->append($this->buildRate(0, $netFreightCharge, $this->getConfigData('residentialratetext'),  $this->getConfigData('residentialfee'), 1));
				}
				else {
					$result->append($this->buildRate(0, $netFreightCharge, $this->getConfigData('methodtext'), 0, 0));
				}
			}
		}

		return $result;
	}

	public function buildRate($expectedArrivalDateString, $netFreightChargeAmt, $methodText, $rDeliveryAmt, $idx) {
		$rate = Mage::getModel('shipping/rate_result_method');
		$method = $rate['service'];
		$rate->setCarrier($this->_getCarrierCode());
		$rate->setCarrierTitle($this->getConfigData('title'));
		$rate->setMethod($method+$idx);
		$deliveryDateMsg = "";
		if ($this->getConfigFlag('showdeliverydatemsg')){
			$deliveryDateMsg =	htmlspecialchars("(Expected delivery by $expectedArrivalDateString)");
		}

		$rate->setMethodTitle($methodText." ".$deliveryDateMsg);
		$rate->setCost($this->getMethodPrice($netFreightChargeAmt+$rDeliveryAmt, '') );
		$rate->setPrice($this->getMethodPrice($netFreightChargeAmt+$rDeliveryAmt, ''));
		return $rate;
	}

	public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
		$this->setRequest($request);
		$this->_result = $this->_getQuotes(false);
		if ($this->getConfigFlag('showResidentialFee')){
			Mage::log('showing residential fee..........');
			$this->_result->append($this->_getQuotes(true));
		}
		if (!$this->getConfigFlag('active') || (intval($this->_rawRequest->getWeight()) < $this->getConfigData('activation_weight'))) {
			return false;
		}
		return $this->getResult();
	}

	public function getMethodPrice($cost, $method='')
	{
		if ($method == $this->getConfigData('free_method') &&
		$this->getConfigData('free_shipping_enable') &&
		$this->getConfigData('free_shipping_subtotal') < $this->_rawRequest->getValueWithDiscount())
		{
			$price = '0.00';
		} else {
			$priceWithFee1 = $this->getFinalPriceWithHandlingFees($cost, $this->getConfigData('handling1_fee'), $this->getConfigData('handling1_type'), $this->getConfigData('handling1_action'));
			$price = $this->getFinalPriceWithHandlingFees($priceWithFee1, $this->getConfigData('handling2_fee'), $this->getConfigData('handling2_type'), $this->getConfigData('handling2_action'));
		}
		return $price;
	}

	public function getFinalPriceWithHandlingFees($cost, $handlingFee, $handlingType, $handlingAction)
	{
		//$handlingFee = $this->getConfigData('handling2_fee');
		$finalMethodPrice = 0;
		//$handlingType = $this->getConfigData('handling2_type');
		if (!$handlingType) {
			$handlingType = self::HANDLING_TYPE_FIXED;
		}
		//$handlingAction = $this->getConfigData('handling2_action');
		if (!$handlingAction) {
			$handlingAction = self::HANDLING_ACTION_PERORDER;
		}
		if($handlingAction == self::HANDLING_ACTION_PERPACKAGE)
		{
			if ($handlingType == self::HANDLING_TYPE_PERCENT) {
				$finalMethodPrice = ($cost + ($cost * $handlingFee/100)) * $this->_numBoxes;
			} else {
				$finalMethodPrice = ($cost + $handlingFee) * $this->_numBoxes;
			}
		} else {
			if ($handlingType == self::HANDLING_TYPE_PERCENT) {
				$finalMethodPrice = ($cost * $this->_numBoxes) + ($cost * $this->_numBoxes * $handlingFee/100);
			} else {
				$finalMethodPrice = ($cost * $this->_numBoxes) + $handlingFee;
			}
		}
		return $finalMethodPrice;
	}

	public function setRequest(Mage_Shipping_Model_Rate_Request $request) {
		$this->_request = $request;
		$r = new Varien_Object();

		if ($request->getOrigCountry()) {
			$origCountry = $request->getOrigCountry();
		} else {
			$origCountry = Mage :: getStoreConfig('shipping/origin/country_id', $this->getStore());
		}
		$r->setOrigCountry(Mage :: getModel('directory/country')->load($origCountry)->getIso2Code());

		if ($request->getOrigRegionCode()) {
			$origRegionCode = $request->getOrigRegionCode();
		} else {
			$origRegionCode = Mage :: getStoreConfig('shipping/origin/region_id', $this->getStore());
			if (is_numeric($origRegionCode)) {
				$origRegionCode = Mage :: getModel('directory/region')->load($origRegionCode)->getCode();
			}
		}
		$r->setOrigRegionCode($origRegionCode);

		if ($request->getOrigPostcode()) {
			$r->setOrigPostal($request->getOrigPostcode());
		} else {
			$r->setOrigPostal(Mage :: getStoreConfig('shipping/origin/postcode', $this->getStore()));
		}

		if ($request->getOrigCity()) {
			$r->setOrigCity($request->getOrigCity());
		} else {
			$r->setOrigCity(Mage :: getStoreConfig('shipping/origin/city', $this->getStore()));
		}

		if ($request->getDestCountryId()) {
			$destCountry = $request->getDestCountryId();
		} else {
			$destCountry = self :: USA_COUNTRY_ID;
		}

		//for UPS, puero rico state for US will assume as puerto rico country
		if ($destCountry == self :: USA_COUNTRY_ID && ($request->getDestPostcode() == '00912' || $request->getDestRegionCode() == self :: PUERTORICO_COUNTRY_ID)) {
			$destCountry = self :: PUERTORICO_COUNTRY_ID;
		}

		$r->setDestCountry(Mage :: getModel('directory/country')->load($destCountry)->getIso2Code());

		$r->setDestRegionCode($request->getDestRegionCode());

		if ($request->getDestPostcode()) {
			$r->setDestPostal($request->getDestPostcode());
		} else {

		}

		$weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
		$r->setWeight($weight);

		$this->_rawRequest = $r;

		return $this;
	}

	public function getResult() {
		return $this->_result;
	}

	public function getCode($type, $code = '') {
		$codes = array (
            'method'=>array(
                'STANDARD' => 'Standard Service (commercial delivery)',
		),
			'freight_class' => array (
				'50' => '50',
				'55' => '55',
				'60' => '60',
				'65' => '65',
				'70' => '70',
				'77.5' => '77.5',
				'85' => '85',
				'92.5' => '92.5',
				'100' => '100',
				'110' => '110',
				'125' => '125',
				'150' => '150',
				'175' => '175',
				'200' => '200',
				'250' => '250',
				'300' => '300',
				'400' => '400',
				'500' => '500',
		),
		'package_type' => array (
				'BAG' => 'Bag',
				'BAL' => 'Bale',
				'BAR' => 'Barrel',
				'BDL' => 'Bundle',
				'BIN' => 'Bin',
				'BOX' => 'Box',
				'BSK' => 'Basket',
				'BUN' => 'Bunch',
				'CAB' => 'Cabinet',
				'CAN' => 'Can',
				'CAR' => 'Carrier',
				'CAS' => 'Case',
				'CBY' => 'Carboy',
				'CON' => 'Container',
				'CRT' => 'Crate',
				'CSK' => 'Cask',
				'CTN' => 'Carton',
				'CYL' => 'Cylinder',
				'DRM' => 'Drum',
				'LOO' => 'Loose',
				'OTH' => 'Other',
				'PAL' => 'Pail',
				'PCS' => 'Pieces',
				'PKG' => 'Package',
				'PLN' => 'Pipe Line',
				'PLT' => 'Pallet',
				'RCK' => 'Rack',
				'REL' => 'Reel',
				'ROL' => 'Roll',
				'SKD' => 'Skid',
				'SPL' => 'Spool',
				'TBE' => 'Tube',
				'TNK' => 'Tank',
				'UNT' => 'Unit',
				'VPK' => 'Van Pack',
				'WRP' => 'Wrapped',
		),
		'requester_type' => array (
				'10' => 'Prepaid (requires bill to address)',
				'20' => 'Bill to Consignee (requires bill to address)',
				'30' => 'Bill to Third Party (requires bill to address)',
				'40' => 'Freight Collect',
		//'billto'    => 'Bill To (requires a Bill To address)',
		),
		);

		if (!isset ($codes[$type])) {
			//            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid UPS CGI code type: %s', $type));
			return false;
		}
		elseif ('' === $code) {
			return $codes[$type];
		}

		if (!isset ($codes[$type][$code])) {
			//            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid UPS CGI code for type %s: %s', $type, $code));
			return false;
		} else {
			return $codes[$type][$code];
		}
	}

	public function getTracking($trackings) {
		$return = array ();

		if (!is_array($trackings)) {
			$trackings = array (
			$trackings
			);
		}

		$this->_getCgiTracking($trackings);
		return $this->_result;
	}

	protected function _getCgiTracking($trackings)
	{
		//ups no longer support tracking for data streaming version
		//so we can only reply the popup window to ups.
		$result = Mage::getModel('shipping/tracking_result');
		$defaults = $this->getDefaults();
		foreach($trackings as $tracking){
			$status = Mage::getModel('shipping/tracking_result_status');
			$status->setCarrier('ups');
			$status->setCarrierTitle($this->getConfigData('title'));
			$status->setTracking($tracking);
			$status->setPopup(1);
			$status->setUrl("http://wwwapps.ups.com/WebTracking/processInputRequest?HTMLVersion=5.0&error_carried=true&tracknums_displayed=5&TypeOfInquiryNumber=T&loc=en_US&InquiryNumber1=$tracking&AgreeToTermsAndConditions=yes");
			$result->append($status);
		}

		$this->_result = $result;
		return $result;
	}

	/**
	 * Get allowed shipping methods
	 *
	 * @return array
	 */
	public function getAllowedMethods() {
		$allowed = explode(',', $this->getConfigData('allowed_methods'));
		$arr = array ();
		foreach ($allowed as $k) {
			$arr[$k] = $this->getCode('method', $k);
		}
		return $arr;
	}

	public function _doShipmentRequest(Varien_Object $request) {
		//Mage::log("_doShipmentRequest");
		// FUTURE ENHANCEMENT
	}
}