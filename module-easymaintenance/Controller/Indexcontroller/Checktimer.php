<?php

namespace Biztech\Easymaintenance\Controller\Indexcontroller;

class Checktimer extends \Magento\Framework\App\Action\Action {

	protected $_storeConfig;
	protected $_cacheManager;
	protected $_scopeConfig;
	protected $_resource;
	protected $_timezone;
	protected $_date;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\ResourceConnection $resource, 
		\Magento\Framework\Stdlib\DateTime\DateTime $date, 
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone, 
		array $data = []
		) {
		$this->_scopeConfig = $scopeConfig;
		$this->_resource = $resource;
		$this->_date = $date;
		$this->_timezone = $timezone;
		parent::__construct($context);
	}

	function execute() {
		$this->_storeConfig = $this->_objectManager->get('Biztech\Easymaintenance\Model\Config');
		$this->_resourceConfig = $this->_objectManager->get('Magento\Config\Model\ResourceModel\Config');

		$this->_cacheManager = $this->_objectManager->get('Magento\Framework\App\CacheInterface');

		$storeId = $this->_storeConfig->getStoreManager()->getStore()->getId();
		$isEnabled = $this->_storeConfig->getValue('easymaintenance/general/is_enabled');
		$timerEnabled = $this->_storeConfig->getValue('easymaintenance/timer/timer_enabled');
		$makesiteEnabled = $this->_storeConfig->getValue('easymaintenance/timer/site_enabled');

		$end_date = $this->_scopeConfig->getValue('easymaintenance/timer/timer_end_date');
		$_end_time = $this->_scopeConfig->getValue('easymaintenance/timer/timer_end_hour');
		$end_time = preg_replace('/,/', ':', $_end_time);


		$_end_maintenance_time = date("Y-m-d H:i:s", strtotime($end_date . ' ' . $end_time));
		$timeZone = $this->_timezone->getConfigTimezone();
		date_default_timezone_set($timeZone);

		$current_time = date("Y-m-d H:i:s", strtotime($this->_date->gmtDate('m/d/Y H:i:s', $timeZone)));

		if ($isEnabled == 1 && $timerEnabled == 1 && $makesiteEnabled == 1) {
			if ($_end_maintenance_time < $current_time) {
				try {

					$this->_resourceConfig->saveConfig('easymaintenance/general/is_enabled', 0, 'default', 0);
					$this->_resourceConfig->saveConfig('easymaintenance/timer/timer_enabled', 0, 'default', 0);
					$this->_cacheManager->clean();
					// @codingStandardsIgnoreLine
                                        echo true;
				} catch (\Exception $e) {                                    
                                    throw $e;
				}
			}
		} else if ( $isEnabled == 1 && $timerEnabled == 1 && $makesiteEnabled == 0 ){
			try{
				$this->_resourceConfig->saveConfig('easymaintenance/timer/timer_enabled', 0, 'default', 0);
			} catch (\Exception $e){
                                    //$msg = ''; $msg = $e->getMessage(); throw new $e($msg);//echo $e->getMessage();
                            throw $e;
			}
		}
	}

}
