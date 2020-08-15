<?php

namespace Biztech\Easymaintenance\Observer;

use \Biztech\Easymaintenance\Block\Context;
use \Biztech\Easymaintenance\Model\Logger\Logger;
use \Biztech\Easymaintenance\Model\Config;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\App\Request\Http;
use Magento\Framework\Stdlib\CookieManagerInterface;

class Observer implements ObserverInterface {

    protected $_scopeConfig;
    protected $_date;
    protected $_authSession;
    protected $_objectManager;
    protected $_logger;
    protected $_transportBuilder;
    protected $_blockFactory;
    protected $_helperData;
    protected $_timezone;
    protected $cookieInterface;

    /**
     * Magento Code Injection's
     */
    public function __construct(
    Context $context, Logger $logger, Config $scopeConfig, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Magento\Backend\Model\Auth\Session $authSession, \Magento\Framework\ObjectManagerInterface $om, \Magento\Framework\View\Element\BlockFactory $blockFactory, \Biztech\Easymaintenance\Helper\Data $data, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone, CookieManagerInterface $cookieInterface
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_date = $date;
        $this->_authSession = $authSession;
        $this->_objectManager = $om;
        $this->_logger = $logger;
        $this->_timezone = $timezone;
        $this->cookieInterface = $cookieInterface;

        $this->_blockFactory = $blockFactory;
        $this->_helperData = $data;
    }

    /**
     * 
     *  Default execute Block for ObserverInterface
     * @return void|ResponseInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {

        $storeId = $this->_scopeConfig->getStoreId();

        $adminFrontName = $observer->getRequest()->getFrontName('adminhtml');

        $redirect_url = array_map('trim', explode("\n", $this->_scopeConfig->getValue('easymaintenance/general/redirect_url')));

        $area = $observer->getRequest()->getOriginalPathInfo();
        $requestUri = $observer->getRequest()->getRequestUri();

        if (!(in_array($requestUri, $redirect_url)) && (!preg_match('/postnotify/', $area)) && (!preg_match('/postfeedback/', $area)) && (!preg_match('/checktimer/', $area)) && (!preg_match('/updatetime/', $area))) {

            //$isSiteLicensed = $this->_helperData->isEnabled();
		$isSiteLicensed = true;
            $isEnabled = $this->_scopeConfig->getValue('easymaintenance/general/is_enabled');
            $timerEnabled = $this->_scopeConfig->getValue('easymaintenance/timer/timer_enabled');
            $makesiteEnabled = $this->_scopeConfig->getValue('easymaintenance/timer/site_enabled');

            $start_date = $this->_scopeConfig->getValue('easymaintenance/timer/timer_start_date');
            $_start_time = $this->_scopeConfig->getValue('easymaintenance/timer/timer_start_hour');
            $start_time = preg_replace('/,/', ':', $_start_time);

            $end_date = $this->_scopeConfig->getValue('easymaintenance/timer/timer_end_date');
            $_end_time = $this->_scopeConfig->getValue('easymaintenance/timer/timer_end_hour');
            $end_time = preg_replace('/,/', ':', $_end_time);

            // $_start_maintenance_time = strtotime( $start_date . ' ' . $start_time );
            $_start_maintenance_time = date("Y-m-d H:i:s", strtotime($start_date . ' ' . $start_time));
            // $_end_maintenance_time = strtotime( $end_date . ' ' . $end_time );
            $_end_maintenance_time = date("Y-m-d H:i:s", strtotime($end_date . ' ' . $end_time));
            $timeZone = $this->_timezone->getConfigTimezone();
            date_default_timezone_set($timeZone);
            // $current_time = strtotime($this->_date->gmtDate('m/d/Y H:i:s', $timeZone));
            $current_time = date("Y-m-d H:i:s", strtotime($this->_date->gmtDate('m/d/Y H:i:s', $timeZone)));

            $currentIP = $observer->getRequest()->getClientIp();
            if ($isSiteLicensed) {
                if ($isEnabled && $timerEnabled /*&& $makesiteEnabled*/ ) {
                    if ($_start_maintenance_time <= $current_time && $_end_maintenance_time > $current_time) {
                        $this->_maintenanceLayout($currentIP, $storeId, $observer);
                    } /*else if ( $isEnabled && $timerEnabled && !$makesiteEnabled ){
                        $this->_maintenanceLayout($currentIP, $storeId, $observer);
                    }*/
                } else if( $isEnabled ) {
                    $this->_maintenanceLayout($currentIP, $storeId, $observer);
                }
            }
        }
    }
    
    /**
     * 
     * @return void|ResponseInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function _maintenanceLayout($currentIP, $storeId, $observer) {
        $allowedIPs = $this->_scopeConfig->getValue('easymaintenance/general/allowedIPs');
        $allowedIPs = preg_replace('/ /', '', $allowedIPs);
        $IPs = array();

        if ('' !== trim($allowedIPs)) {
            $IPs = explode(',', $allowedIPs);
        }

        $allowForAdmin = $this->_scopeConfig->getValue('easymaintenance/general/allowforadmin');

        if ($allowedIPs || $allowForAdmin) {
            $currentIP = $this->bc_getRemoteAddress();

            if ($currentIP == '') {
                $currentIP = $observer->getRequest()->getClientIp();
            }
        }
        $adminIP = null;
        /* -- Admin Session IP Address -- */
        if ($allowForAdmin == 1) {
            
            if ($this->cookieInterface->getCookie(\Biztech\Easymaintenance\Observer\Auth::ADMIN_SESSION_LOGIN)) {
                $auth = $this->_authSession->getRemoteAddress();
                $adminIP = $this->bc_getRemoteAddress();
            }
//            $auth = $this->_authSession->getRemoteAddress();
//            $adminIP = $this->bc_getRemoteAddress();
        }

        if ($currentIP === $adminIP) {
            $this->createLog('Access denied for IP: ' . $currentIP . ' and store ' . $storeId);
        } else {
            if (!in_array($currentIP, $IPs)) {
                $this->createLog('Access granted for IP: ' . $currentIP . ' and store ' . $storeId);

                $html = $this->_blockFactory->createBlock('Biztech\Easymaintenance\Block\Indexcontroller\Indexaction')->setTemplate('Biztech_Easymaintenance::easymaintenance/easymaintenance.phtml')->toHtml();

                if ($this->_scopeConfig->getValue('easymaintenance/contactus/active')) :
                    $html .= $this->_blockFactory->createBlock('Biztech\Easymaintenance\Block\Indexcontroller\Indexaction')->setTemplate('Biztech_Easymaintenance::easymaintenance/popup_html.phtml')->toHtml();
                endif;

                if ($this->_scopeConfig->getValue('easymaintenance/notify/active')) :
                    $html .= $this->_blockFactory->createBlock('Biztech\Easymaintenance\Block\Indexcontroller\Indexaction')->setTemplate('Biztech_Easymaintenance::easymaintenance/notify.phtml')->toHtml();
                endif;

                $html .= $this->_blockFactory->createBlock('Biztech\Easymaintenance\Block\Indexcontroller\Indexaction')->setTemplate('Biztech_Easymaintenance::easymaintenance/footer.phtml')->toHtml();

                if ('' != $html) {
                    $response = $this->_objectManager->get('Magento\Framework\HTTP\PhpEnvironment\Response');
                    $response->setHttpResponseCode(503);
                    $response->setHeader('Status', '503 Service Temporarily Unavailable', true);
                    $response->setHeader('Retry-After', '5000', true);
                    $response->sendHeaders();
                    $response->setBody($html);                    
                    // @codingStandardsIgnoreLine
                    echo $html;
                }
                //return $response;
                // @codingStandardsIgnoreLine
                exit();
            } else {
                $this->createLog('Access granted for IP: ' . $currentIP . ' and store ' . $storeId);
            }
        }
    }

    public function createLog($text = null) {
        $logFileName = trim($this->_scopeConfig->getValue('easymaintenance/general/logFileName'));
        if ($logFileName) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/' . $logFileName);
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            return $logger->debug($text);
        }
        return $this->_logger->debug($text);
    }

    public function bc_getRemoteAddress() {
        /** @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $ra */
        $ra = $this->_objectManager->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');
        return $ra->getRemoteAddress();
    }

}
