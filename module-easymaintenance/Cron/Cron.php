<?php

namespace Biztech\Easymaintenance\Cron;

use \Biztech\Easymaintenance\Model\Config;

class Cron {

    const XML_PATH_EMAIL_SENDER = 'trans_email/ident_custom2/email';

    protected $_scopeConfig;
    protected $_transportBuilder;
    protected $_date;
    protected $_logger;
    protected $_timezone;
    protected $_inlineTranslation;
    protected $_escaper;

    function __construct(
    Config $scopeConfig, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Biztech\Easymaintenance\Model\Logger\Logger $logger, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\Escaper $escaper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_date = $date;
        $this->_logger = $logger;
        $this->_timezone = $timezone;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_escaper = $escaper;
    }

    public function execute() {
        $this->_inlineTranslation->suspend();
        $storeId = $this->_scopeConfig->getStoreId();

        $isEnabled = $this->_scopeConfig->getValue('easymaintenance/general/is_enabled');

        $end_date = $this->_scopeConfig->getValue('easymaintenance/timer/timer_end_date');
        $_end_time = $this->_scopeConfig->getValue('easymaintenance/timer/timer_end_hour');
        $end_time = preg_replace('/,/', ':', $_end_time);


        if ($isEnabled) {
            $timeZone = $this->_timezone->getConfigTimezone();
            $_end_maintenance_time = date("Y-m-d H:i:s", strtotime($end_date . ' ' . $end_time));
            $current_time = date("Y-m-d H:i:s", strtotime($this->_date->gmtDate('m/d/Y H:i:s', $timeZone)));

            /* $minutes_diff = (int) round(abs(strtotime($_end_maintenance_time) - strtotime($current_time)) / 60 );
              $alertMin = (int) $this->_scopeConfig->getValue('easymaintenance/timer/timer_alert'); */

            $alertMin = (int) $this->_scopeConfig->getValue('easymaintenance/timer/timer_alert');
            $minutes_diff = (int) round((abs(strtotime($_end_maintenance_time) - strtotime($current_time)) / 60 ) / 60);

            if ($minutes_diff <= $alertMin) {
                $fromEmail['name'] = $this->_escaper->escapeHtml('Maintenance');
                $fromEmail['email'] = $this->_escaper->escapeHtml($this->_scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER));
                $toEmail = $this->_escaper->escapeHtml($this->_scopeConfig->getValue('easymaintenance/timer/timer_email'));
                $message = $this->_scopeConfig->getValue('easymaintenance/timer/timer_email_template');

                $this->_logger->error($fromEmail['name'] . $fromEmail['email'] . $toEmail . $message);

                try {
                    $this->_logger->error('bingo before mail');

                    $mail = $this->_transportBuilder->setTemplateIdentifier('easymaintenance_alert_template')
                            // ->setTemplateModel('Magento\Email\Model\BackendTemplate')
                            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_scopeConfig->getStoreId()])
                            ->setTemplateVars([
                                'store' => $this->_scopeConfig->getStore(),
                                'message' => $message
                            ])
                            ->setFrom($fromEmail)
                            ->addTo($toEmail)
                            ->setReplyTo($toEmail)
                            ->getTransport();

                    $this->_logger->error($mail);
                    $mail->sendMessage();
                    $this->_inlineTranslation->resume();
                } catch (\Exception $e) {
                    $this->_logger->debug($e->getMessage());                   
                }
            }
        }
    }

}
