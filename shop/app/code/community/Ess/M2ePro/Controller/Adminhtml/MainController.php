<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Controller_Adminhtml_MainController extends Ess_M2ePro_Controller_Adminhtml_BaseController
{
    //#############################################

    public function preDispatch()
    {
        parent::preDispatch();

        if ($this->getRequest()->isXmlHttpRequest() &&
            !Mage::getSingleton('admin/session')->isLoggedIn()) {

            exit(json_encode( array(
                'error' => true,
                'message' => Mage::helper('M2ePro')->__('You have logged out. Refresh page please.')
            )));
        }

        if ($this->getRequest()->isGet() &&
            !$this->getRequest()->isPost() &&
            !$this->getRequest()->isXmlHttpRequest() &&
            Mage::getSingleton('M2ePro/Wizard')->isInstallationFinished()) {

            try {
                Mage::getModel('M2ePro/License_Server')->updateStatus(false);
                Mage::getModel('M2ePro/License_Server')->updateLock(false);
                Mage::getModel('M2ePro/License_Server')->updateMessages(false);
            } catch (Exception $exception) {}
        }

        return $this;
    }

    public function dispatch($action)
    {
        try {

            $this->getRequest()->isGet() &&
            !$this->getRequest()->isPost() &&
            !$this->getRequest()->isXmlHttpRequest() &&
            $this->updateBackupConnectionData();

            Mage::helper('M2ePro/Exception')->setFatalErrorHandler();
            return parent::dispatch($action);

        } catch (Exception $exception) {

            if ($this->getRequest()->getControllerName() == 'adminhtml_support') {
                exit($exception->getMessage());
            } else {

                if (Mage::helper('M2ePro/Server')->isDeveloper()) {
                    throw $exception;
                } else {

                    Mage::helper('M2ePro/Exception')->process($exception,true);

                    if (($this->getRequest()->isGet() || $this->getRequest()->isPost()) &&
                        !$this->getRequest()->isXmlHttpRequest()) {

                        $this->_getSession()->addError(Mage::helper('M2ePro/Exception')->getUserMessage($exception));
                        $this->_redirect('*/adminhtml_support/index');
                    } else {
                        exit($exception->getMessage());
                    }
                }
            }
        }
    }

    //#############################################

    public function loadLayout($ids=null, $generateBlocks=true, $generateXml=true)
    {
        if ($this->getRequest()->isGet() &&
            !$this->getRequest()->isPost() &&
            !$this->getRequest()->isXmlHttpRequest()) {

            $lockNotification = $this->addLockNotifications();

            $lockNotification ||
            !Mage::getSingleton('M2ePro/Wizard')->isInstallationFinished() ||
            $this->addLicenseActivationNotifications() ||
            $this->addLicenseValidationFailNotifications() ||
            $this->addLicenseModesNotifications() ||
            $this->addLicenseStatusesNotifications() ||
            $this->addLicenseExpirationDatesNotifications() ||
            $this->addLicenseTrialNotifications() ||
            $this->addLicensePreExpirationDateNotifications();

            $this->addServerNotifications();
            $this->addBrowserNotifications();

            $lockNotification ||
            !Mage::getSingleton('M2ePro/Wizard')->isInstallationFinished() ||
            Mage::helper('M2ePro/Server')->isDeveloper() ||
            $this->addCronNotifications();

            $lockNotification ||
            !Mage::getSingleton('M2ePro/Wizard')->isInstallationFinished() ||
            Mage::helper('M2ePro/Server')->isDeveloper() ||
            $this->addCronErrors();

            $lockNotification ||
            !Mage::getSingleton('M2ePro/Wizard')->isInstallationFinished() ||
            $this->addFeedbackNotifications();

            $lockNotification ||
            !Mage::getSingleton('M2ePro/Wizard')->isInstallationFinished() ||
            $this->addMyMessageNotifications();
        }

        is_array($ids) ? $ids[] = 'm2epro' : $ids = array('default','m2epro');
        return parent::loadLayout($ids, $generateBlocks, $generateXml);
    }

    protected function _addContent(Mage_Core_Block_Abstract $block)
    {
        if ($this->getRequest()->isGet() &&
            !$this->getRequest()->isPost() &&
            !$this->getRequest()->isXmlHttpRequest() &&
            Mage::getModel('M2ePro/License_Model')->isLock()) {
            return $this;
        }

        $blockGeneral = $this->getLayout()->createBlock('M2ePro/adminhtml_general');
        $this->getLayout()->getBlock('content')->append($blockGeneral);

        $this->addWizardUpgradeNotification();

        return parent::_addContent($block);
    }

    //#############################################

    private function addLockNotifications()
    {
        if (Mage::getModel('M2ePro/License_Model')->isLock()) {
            $this->_getSession()->addError(
                Mage::helper('M2ePro')->__('M2E Pro module is locked because of security reason. Please contact us.')
            );
            return true;
        }
        return false;
    }

    private function addServerNotifications()
    {
        $messages = Mage::getModel('M2ePro/License_Model')->getMessages();

        foreach ($messages as $message) {

            if (isset($message['text']) && isset($message['type']) && $message['text'] != '') {

                switch ($message['type']) {
                    case Ess_M2ePro_Model_License_Model::MESSAGE_TYPE_NOTICE:
                        $this->_getSession()->addNotice(Mage::helper('M2ePro')->__($message['text']));
                        break;
                    case Ess_M2ePro_Model_License_Model::MESSAGE_TYPE_ERROR:
                        $this->_getSession()->addError(Mage::helper('M2ePro')->__($message['text']));
                        break;
                    case Ess_M2ePro_Model_License_Model::MESSAGE_TYPE_WARNING:
                        $this->_getSession()->addWarning(Mage::helper('M2ePro')->__($message['text']));
                        break;
                    case Ess_M2ePro_Model_License_Model::MESSAGE_TYPE_SUCCESS:
                        $this->_getSession()->addSuccess(Mage::helper('M2ePro')->__($message['text']));
                        break;
                    default:
                        $this->_getSession()->addNotice(Mage::helper('M2ePro')->__($message['text']));
                        break;
                }
            }

        }
    }

    private function addBrowserNotifications()
    {
        // Check MS Internet Explorer 6
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.') !== false) {
            $helper = Mage::helper('M2ePro');
            $warning  = $helper->__('Magento and M2E Pro has Internet Explorer 7 as minimal browser requirement. ');
            $warning .= $helper->__('Please upgrade your browser.');
            $this->_getSession()->addWarning($warning);
            return true;
        }
        return false;
    }

    // --------------------------------------------

    private function addLicenseActivationNotifications()
    {
        if (!Mage::getModel('M2ePro/License_Model')->getKey() ||
            !Mage::getModel('M2ePro/License_Model')->getDomain() ||
            !Mage::getModel('M2ePro/License_Model')->getIp() ||
            !Mage::getModel('M2ePro/License_Model')->getDirectory()) {

            $message = Mage::helper('M2ePro')
                ->__('M2E Pro module requires activation. Go to the %sl%license page%el%.');

            $startLink = '<a href="'.$this->getUrl("*/adminhtml_license/index").'" target="_blank">';
            $endLink = '</a>';

            $this->_getSession()->addError(str_replace(array('%sl%','%el%'),
                                                       array($startLink,$endLink),
                                                       $message));
            return true;
        }

        return false;
    }

    private function addLicenseValidationFailNotifications()
    {
        // MAGENTO GO UGLY HACK
        //#################################
        if (Mage::helper('M2ePro/Magento')->isGoEdition()) {
            return false;
        }
        //#################################

        $domainNotify = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue(
            '/license/validation/domain/notification/', 'mode'
        );

        if ($domainNotify &&
            Mage::getModel('M2ePro/License_Model')->getDomain() != Mage::helper('M2ePro/Server')->getDomain()) {

            $message  = Mage::helper('M2ePro')->__('M2E Pro license key validation is failed for this domain. ');
            $message .= Mage::helper('M2ePro')->__('Go to the %sl%license page%el%.');

            $startLink = '<a href="'.$this->getUrl("*/adminhtml_license/index").'" target="_blank">';
            $endLink = '</a>';

            $this->_getSession()->addError(str_replace(array('%sl%','%el%'),
                                                       array($startLink,$endLink),
                                                       $message));

            return true;
        }

        $ipNotify = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue(
            '/license/validation/ip/notification/', 'mode'
        );

        if ($ipNotify && Mage::getModel('M2ePro/License_Model')->getIp() != Mage::helper('M2ePro/Server')->getIp()) {

            $message  = Mage::helper('M2ePro')->__('M2E Pro license key validation is failed for this IP. ');
            $message .= Mage::helper('M2ePro')->__('Go to the %sl%license page%el%.');

            $startLink = '<a href="'.$this->getUrl("*/adminhtml_license/index").'" target="_blank">';
            $endLink = '</a>';

            $this->_getSession()->addError(str_replace(array('%sl%','%el%'),
                                                       array($startLink,$endLink),
                                                       $message));
            return true;
        }

        $directoryNotify = (bool)(int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue(
            '/license/validation/directory/notification/', 'mode'
        );

        $licenseDirectory = Mage::getModel('M2ePro/License_Model')->getDirectory();

        if ($directoryNotify &&
            $licenseDirectory != Mage::helper('M2ePro/Server')->getBaseDirectory()) {

            $message  = Mage::helper('M2ePro')->__('M2E Pro license key validation is failed for this base directory.');
            $message .= Mage::helper('M2ePro')->__(' Go to the %sl%license page%el%.');

            $startLink = '<a href="'.$this->getUrl("*/adminhtml_license/index").'" target="_blank">';
            $endLink = '</a>';

            $this->_getSession()->addError(str_replace(array('%sl%','%el%'),
                                                       array($startLink,$endLink),
                                                       $message));
            return true;
        }

        return false;
    }

    private function addLicenseModesNotifications()
    {
        $hasMessage = false;

        foreach (Mage::helper('M2ePro/Component')->getActiveComponents() as $component) {

            if (Mage::getModel('M2ePro/License_Model')->isNoneMode($component)) {

                $message  = Mage::helper('M2ePro')->__('M2E Pro module requires activation for "%com%" component. ');
                $message .= Mage::helper('M2ePro')->__('Go to the %sl%license page%el%.');

                $startLink = '<a href="'.$this->getUrl("*/adminhtml_license/index").'" target="_blank">';
                $endLink = '</a>';

                $this->_getSession()->addError(str_replace(array('%sl%','%el%','%com%'),
                                                           array($startLink,$endLink,ucwords($component)),
                                                           $message));
                $hasMessage = true;
            }
        }

        return $hasMessage;
    }

    private function addLicenseStatusesNotifications()
    {
        $hasMessage = false;

        foreach (Mage::helper('M2ePro/Component')->getActiveComponents() as $component) {

            if (Mage::getModel('M2ePro/License_Model')->isSuspendedStatus($component)) {

                $message  = Mage::helper('M2ePro')->__('M2E Pro module license suspended for "%com%" component. ');
                $message .= Mage::helper('M2ePro')->__('Go to the %sl%license page%el%.');

                $startLink = '<a href="'.$this->getUrl("*/adminhtml_license/index").'" target="_blank">';
                $endLink = '</a>';

                $this->_getSession()->addError(str_replace(array('%sl%','%el%','%com%'),
                                                           array($startLink,$endLink,ucwords($component)),
                                                           $message));
                $hasMessage = true;
            }

            if (Mage::getModel('M2ePro/License_Model')->isClosedStatus($component)) {

                $message  = Mage::helper('M2ePro')->__('M2E Pro module license closed for "%com%" component. ');
                $message .= Mage::helper('M2ePro')->__('Go to the %sl%license page%el%.');

                $startLink = '<a href="'.$this->getUrl("*/adminhtml_license/index").'" target="_blank">';
                $endLink = '</a>';

                $this->_getSession()->addError(str_replace(array('%sl%','%el%','%com%'),
                                                           array($startLink,$endLink,ucwords($component)),
                                                           $message));
                $hasMessage = true;
            }
        }

        return $hasMessage;
    }

    private function addLicenseExpirationDatesNotifications()
    {
        $hasMessage = false;

        foreach (Mage::helper('M2ePro/Component')->getActiveComponents() as $component) {

            if (Mage::getModel('M2ePro/License_Model')->isExpirationDate($component)) {

                $message  = Mage::helper('M2ePro')->__('M2E Pro module license has expired for "%com%" component. ');
                $message .= Mage::helper('M2ePro')->__('Go to the %sl%license page%el%.');

                $startLink = '<a href="'.$this->getUrl("*/adminhtml_license/index").'" target="_blank">';
                $endLink = '</a>';

                $this->_getSession()->addError(str_replace(array('%sl%','%el%','%com%'),
                                                           array($startLink,$endLink,ucwords($component)),
                                                           $message));
                $hasMessage = true;
            }
        }

        return $hasMessage;
    }

    private function addLicenseTrialNotifications()
    {
        $hasMessage = false;

        foreach (Mage::helper('M2ePro/Component')->getActiveComponents() as $component) {

            if (Mage::getModel('M2ePro/License_Model')->isTrialMode($component)) {

                $message  = Mage::helper('M2ePro')->__('M2E Pro module is running under Trial License ');
                $message .= Mage::helper('M2ePro')->__('for "%com%" component, that will expire on ');
                $message .= Mage::getModel('M2ePro/License_Model')->getTextExpirationDate($component).'.';

                $this->_getSession()->addWarning(str_replace(array('%com%'),array(ucwords($component)),$message));

                $hasMessage = true;
            }
        }

        return $hasMessage;
    }

    private function addLicensePreExpirationDateNotifications()
    {
        $hasMessage = false;

        foreach (Mage::helper('M2ePro/Component')->getActiveComponents() as $component) {

            if (Mage::getModel('M2ePro/License_Model')->getIntervalBeforeExpirationDate($component) <= 60*60*24*3) {

                $message  = Mage::helper('M2ePro')->__('M2E Pro module license will expire on ');
                $message .= Mage::getModel('M2ePro/License_Model')->getTextExpirationDate($component);
                $message .= ' for "%com%" component. ';
                $message .= Mage::helper('M2ePro')->__('Go to the %sl%license page%el%.');

                $startLink = '<a href="'.$this->getUrl("*/adminhtml_license/index").'" target="_blank">';
                $endLink = '</a>';

                $this->_getSession()->addWarning(str_replace(array('%sl%','%el%','%com%'),
                                                             array($startLink,$endLink,ucwords($component)),
                                                             $message));
                $hasMessage = true;
            }
        }

        return $hasMessage;
    }

    // --------------------------------------------

    private function addCronNotifications()
    {
        if (Mage::getModel('M2ePro/Cron')->isShowNotification()) {

            $allowedInactiveHours = (int)Mage::helper('M2ePro/Module')->getConfig()->getGroupValue(
                '/cron/notification/', 'max_inactive_hours'
            );

            $message  = Mage::helper('M2ePro')->__('Attention! Last eBay AUTOMATIC synchronization was performed ');
            $message .= Mage::helper('M2ePro')->__('by cron more than %hr% hours ago. You should set up cron job, ');
            $message .= Mage::helper('M2ePro')->__('otherwise no automatic synchronization will be performed. ');
            $message .= Mage::helper('M2ePro')->__('<br/>You can check this %sla%article%el% to get ');
            $message .= Mage::helper('M2ePro')->__('how to set cron job. ');
            $message .= Mage::helper('M2ePro')->__('The command for cron job for your server environment can ');
            $message .= Mage::helper('M2ePro')->__('be found %slh%here%el%.');

            $tempUrl  = 'http://support.m2epro.com/knowledgebase/articles/';
            $tempUrl .= '42054-how-to-set-up-cron-job-for-m2e-pro-';
            $startLinkArticle = '<a href="'.$tempUrl.'" target="_blank">';

            $tempUrl  = $this->getUrl("*/adminhtml_about/index").'#magento_block_about_cron_info';
            $startLinkHere = '<a href="'.$tempUrl.'" target="_blank">';

            $endLink = '</a>';

            $this->_getSession()->addNotice(str_replace(array('%hr%','%sla%','%slh%','%el%'),
                                                        array($allowedInactiveHours,
                                                              $startLinkArticle,
                                                              $startLinkHere,
                                                              $endLink),$message));
            return true;
        }

        return false;
    }

    private function addCronErrors()
    {
        if (Mage::getModel('M2ePro/Cron')->isShowError()) {

            $message  = Mage::helper('M2ePro')->__('Attention! The Cron job is not running at the moment. ');
            $message .= Mage::helper('M2ePro')->__('The Amazon Integration DOES NOT WORK without automatic ');
            $message .= Mage::helper('M2ePro')->__('task scheduled by cron job. <br/>You can check this ');
            $message .= Mage::helper('M2ePro')->__('%sla%article%el% to get better idea why cron job is mandatory. ');
            $message .= Mage::helper('M2ePro')->__('The command for cron job for your server environment can ');
            $message .= Mage::helper('M2ePro')->__('be found %slh%here%el%.');

            $tempUrl  = 'http://support.m2epro.com/knowledgebase/articles/';
            $tempUrl .= '112436-why-cron-job-is-required-for-amazon-integration-';
            $startLinkArticle = '<a href="'.$tempUrl.'" target="_blank">';

            $tempUrl  = $this->getUrl("*/adminhtml_about/index").'#magento_block_about_cron_info';
            $startLinkHere = '<a href="'.$tempUrl.'" target="_blank">';

            $endLink = '</a>';

            $this->_getSession()->addError(str_replace(array('%sla%','%slh%','%el%'),
                                                        array($startLinkArticle,
                                                              $startLinkHere,
                                                              $endLink),$message));
            return true;
        }

        return false;
    }

    private function addFeedbackNotifications()
    {
        if (Mage::getModel('M2ePro/Ebay_Feedback')->haveNew(true)) {

            $message  = Mage::helper('M2ePro')->__('New buyer negative feedback(s) was received. ');
            $message .= Mage::helper('M2ePro')->__('Go to the %sl%feedbacks page%el%.');

            $startLink = '<a href="'.$this->getUrl('*/adminhtml_ebay_feedback/index').'" target="_blank">';
            $endLink = '</a>';

            $this->_getSession()->addNotice(str_replace(array('%sl%','%el%'),
                                                        array($startLink,$endLink),
                                                        $message));
            return true;
        }

        return false;
    }

    private function addMyMessageNotifications()
    {
        if (Mage::getModel('M2ePro/Ebay_Message')->haveNew()) {

            $message  = Mage::helper('M2ePro')->__('New buyer message(s) was received. ');
            $message .= Mage::helper('M2ePro')->__('Go to the %sl%messages page%el%.');

            $startLink = '<a href="'.$this->getUrl("*/adminhtml_ebay_message/index").'" target="_blank">';
            $endLink = '</a>';

            $this->_getSession()->addNotice(str_replace(array('%sl%','%el%'),
                                                        array($startLink,$endLink),
                                                        $message));
            return true;
        }

        return false;
    }

    // --------------------------------------------

    private function addWizardUpgradeNotification()
    {
        if ($this->getRequest()->getParam('hide_upgrade_notification') == 'yes' ||
            $this->getRequest()->getControllerName() == 'adminhtml_wizard') {
            return;
        }

        if (!Mage::getSingleton('M2ePro/Wizard')->isInstallationFinished() ||
            Mage::getSingleton('M2ePro/Wizard')->isUpgradeFinished()) {
            return;
        }

        $this->getLayout()->getBlock('head')->addJs('M2ePro/WizardHandler.js');

        // Video tutorial
        //-------------
        $this->getLayout()->getBlock('head')
            ->addItem('js_css', 'prototype/windows/themes/default.css')
            ->addJs('prototype/window.js');

        if (Mage::helper('M2ePro/Magento')->isCommunityEdition()) {
            version_compare(Mage::getVersion(), '1.7.0.0', '>=')
                ? $this->getLayout()->getBlock('head')->addCss('lib/prototype/windows/themes/magento.css')
                : $this->getLayout()->getBlock('head')->addItem('js_css', 'prototype/windows/themes/magento.css');
        } else {
            $this->getLayout()->getBlock('head')->addCss('lib/prototype/windows/themes/magento.css');
            $this->getLayout()->getBlock('head')->addItem('js_css', 'prototype/windows/themes/magento.css');
        }

        $this->getLayout()->getBlock('head')->addJs('M2ePro/VideoTutorialHandler.js');
         //-------------

        $blockNotification = $this->getLayout()->createBlock('M2ePro/adminhtml_wizard_upgrade_notification');
        $this->getLayout()->getBlock('content')->append($blockNotification);
    }

    //#############################################

    private function updateBackupConnectionData()
    {
        $dateLastCheck = Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/backups/', 'date_last_check');

        if (is_null($dateLastCheck)) {
            $dateLastCheck = Mage::helper('M2ePro')->getCurrentGmtDate(true)-60*60*365;
        } else {
            $dateLastCheck = strtotime($dateLastCheck);
        }

        if (Mage::helper('M2ePro')->getCurrentGmtDate(true) >= $dateLastCheck + 60*60*24) {

            $domainBackup = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '127.0.0.1';
            strpos($domainBackup,'www.') === 0 && $domainBackup = substr($domainBackup,4);
            Mage::helper('M2ePro/Module')->getConfig()->setGroupValue('/backups/', 'domain', $domainBackup);

            $ipBackup = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : NULL;
            is_null($ipBackup) && $ipBackup = isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : '127.0.0.1';
            Mage::helper('M2ePro/Module')->getConfig()->setGroupValue('/backups/', 'ip', $ipBackup);

            $directoryBackup = Mage::getBaseDir();
            Mage::helper('M2ePro/Module')->getConfig()->setGroupValue('/backups/', 'directory', $directoryBackup);

            Mage::helper('M2ePro/Module')->getConfig()->setGroupValue(
                '/backups/', 'date_last_check', Mage::helper('M2ePro')->getCurrentGmtDate()
            );
        }
    }

    //#############################################
}