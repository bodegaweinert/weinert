<?php
/**
 * Copyright © 2015 Biztechcommerce. All rights reserved.
 */
namespace Biztech\Easymaintenance\Model;

/**
 * Easymaintenance Config model
 */
class Config extends \Magento\Framework\DataObject
{

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;
	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_scopeConfig;
	/**
	 * @var \Magento\Framework\App\Config\ValueInterface
	 */
	protected $_backendModel;
	/**
	 * @var \Magento\Framework\DB\Transaction
	 */
	protected $_transaction;
	/**
	 * @var \Magento\Framework\App\Config\ValueFactory
	 */
	protected $_configValueFactory;
	/**
	 * @var int $_storeId
	 */
	protected $_storeId;
	/**
	 * @var string $_storeCode
	 */
	protected $_storeCode;

	protected $_store;

	protected $_storeName;

	protected $_request;

	/**
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager,
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
	 * @param \Magento\Framework\App\Config\ValueInterface $backendModel,
	 * @param \Magento\Framework\DB\Transaction $transaction,
	 * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory,
	 * @param array $data
	 */
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\Config\ValueInterface $backendModel,
		\Magento\Framework\DB\Transaction $transaction,
		\Magento\Framework\App\Config\ValueFactory $configValueFactory,
		\Magento\Framework\App\RequestInterface $request,
		array $data = []
	) {
		parent::__construct($data);
		$this->_storeManager = $storeManager;
		$this->_scopeConfig = $scopeConfig;
		$this->_backendModel = $backendModel;
		$this->_transaction = $transaction;
		$this->_configValueFactory = $configValueFactory;
		$this->_storeId=(int)$this->_storeManager->getStore()->getId();
		$this->_storeCode=$this->_storeManager->getStore()->getCode();

		$this->_request = $request;
	}

	public function getStoreManager(){
		return $this->_storeManager;
	}

	/**
	 * Function for getting Config value of current store
	 * @param string $path,
	 */
	public function getCurrentStoreConfigValue($path){
		return $this->_scopeConfig->getValue($path,'store',$this->_storeCode);
	}

	/**
	 * Function for setting Config value of current store
	 * @param string $path,
	 * @param string $value,
	 */
	public function setCurrentStoreConfigValue($path,$value){

		$data = [
			'path' => $path,
			'scope' =>  'stores',
			'scope_id' => $this->_storeId,
			'scope_code' => $this->_storeCode,
			'value' => $value,
		];

		$this->_backendModel->addData($data);
		$this->_transaction->addObject($this->_backendModel);
		$this->_transaction->save();
	}

	public function getValue($path){
		return $this->getCurrentStoreConfigValue($path);
	}

	public function setConfigValue($path,$value){
		$this->setCurrentStoreConfigValue($path,$value);
	}

	public function getStoreId(){
		return $this->_storeId;
	}

	public function getStoreCode(){
		return $this->_storeCode;
	}

	public function getStore(){
		$this->_store = $this->_storeManager->getStore();
		return $this->_store;
	}

	public function getStoreName(){
		$this->_storeName = $this->_storeManager->getStore()->getStoreName();
		return $this->_storeName;
	}

}
