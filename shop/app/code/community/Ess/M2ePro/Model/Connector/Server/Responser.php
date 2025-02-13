<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Connector_Server_Responser
{
    /** @var Ess_M2ePro_Model_Processing_Request */
    protected $processingRequest = NULL;

    protected $hash = NULL;
    protected $processingHash = NULL;

    protected $params = array();
    protected $requestData = array();
    protected $performType = Ess_M2ePro_Model_Processing_Request::PERFORM_TYPE_SINGLE;

    protected $messages = array();
    protected $resultType = Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_TYPE_ERROR;

    // ########################################

    public function __construct(Ess_M2ePro_Model_Processing_Request $processingRequest)
    {
        $this->processingRequest = $processingRequest;

        $this->hash = $this->processingRequest->getHash();
        $this->processingHash = $this->processingRequest->getProcessingHash();

        $this->params = $this->processingRequest->getDecodedResponserParams();
        $this->requestData = $this->processingRequest->getDecodedRequestBody();
        $this->performType = $this->processingRequest->getPerformType();
    }

    // ########################################

    public function process(array $responseBody = array(), array $messages = array())
    {
        try {

            $this->processResponseMessages($messages);

            if (!$this->validateResponseData($responseBody)) {
                throw new Exception('Validation Failed. The server response data is not valid.');
            }

            $this->processResponseData($responseBody);

        } catch (Exception $exception) {
            $this->completeUnsuccessfulProcessing($exception->getMessage());
            return false;
        }

        return true;
    }

    //-----------------------------------------

    public function processCompleted(array $responseBody = array(), array $messages = array())
    {
        $result = $this->process($responseBody,$messages);
        $result && $this->completeSuccessfulProcessing();
    }

    public function processFailed($message)
    {
        $this->completeUnsuccessfulProcessing($message);
    }

    //-----------------------------------------

    public function completeSuccessfulProcessing()
    {
        try {
            $this->unsetLocks();
            $this->processingRequest->deleteInstance();
        } catch (Exception $exception) {}
    }

    public function completeUnsuccessfulProcessing($message)
    {
        try {
            $this->unsetLocks(true,$message);
            $this->processingRequest->deleteInstance();
        } catch (Exception $exception) {}
    }

    // ########################################

    abstract protected function unsetLocks($isFailed = false, $message = NULL);

    //-----------------------------------------

    abstract protected function validateResponseData($response);

    abstract protected function processResponseData($response);

    // ########################################

    protected function processResponseMessages(array $messages = array())
    {
        $this->resultType = $this->getResultType($messages);

        foreach ($messages as $message) {
            $this->messages[] = $message;
        }

        if ($this->resultType == Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_TYPE_ERROR) {

            $errorHasSystem = '';

            foreach ($this->messages as $message) {
                if (!isset($message[Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_TYPE_KEY]) ||
                    !isset($message[Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_SENDER_KEY])) {
                    continue;
                }
                $temp1 = Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_TYPE_ERROR;
                $temp2 = Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_SENDER_SYSTEM;
                if ($message[Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_TYPE_KEY] == $temp1 &&
                    $message[Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_SENDER_KEY] == $temp2) {
                    $errorHasSystem != '' && $errorHasSystem .= ', ';
                    $errorHasSystem .= $message[Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_TEXT_KEY];
                }
            }

            if ($errorHasSystem != '') {
                // Parser hack -> Mage::helper('M2ePro')->__('Internal server error(s) [%errors%]');
                throw new Exception("Internal server error(s) [{$errorHasSystem}]");
            }
        }
    }

    protected function getResultType(array $messages = array())
    {
        $types = array();

        foreach ($messages as $message) {
            if (!isset($message[Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_TYPE_KEY])) {
                continue;
            }
            $types[] = $message[Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_TYPE_KEY];
        }

        if (in_array(Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_TYPE_ERROR,$types)) {
            return Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_TYPE_ERROR;
        }
        if (in_array(Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_TYPE_WARNING,$types)) {
            return Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_TYPE_WARNING;
        }

        return Ess_M2ePro_Model_Connector_Server_Protocol::MESSAGE_TYPE_SUCCESS;
    }

    // ########################################
}