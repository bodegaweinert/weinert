<?php
/**
 * Anowave Magento 2 Google Tag Manager Enhanced Ecommerce (UA) Tracking
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Ec
 * @copyright 	Copyright (c) 2018 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Ec\Plugin;

class OrderService
{
	/**
	 * @var \Magento\Framework\App\State
	 */
	protected $state = null;
	
	/**
	 * @var \Anowave\Ec\Model\Api\Measurement\Protocol
	 */
	protected $protocol;
	
	/**
	 * @var \Anowave\Ec\Helper\Data
	 */
	protected $helper;
	
	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;
	
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\State $state
	 * @param \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
	 * @param \Anowave\Ec\Helper\Data $helper
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 */
	public function __construct
	(
		\Magento\Framework\App\State $state,
		\Anowave\Ec\Model\Api\Measurement\Protocol $protocol,
		\Anowave\Ec\Helper\Data $helper,
		\Magento\Framework\Message\ManagerInterface $messageManager
	)
	{
		/**
		 * Set state
		 *
		 * @var \Magento\Framework\App\State $state
		 */
		$this->state = $state;
		
		/**
		 * Set protocol
		 *
		 * @var \Anowave\Ec\Model\Api\Measurement\Protocol $protocol
		 */
		$this->protocol = $protocol;
		
		/**
		 * Set helper
		 *
		 * @var \Anowave\Ec\Helper\Data $helper
		 */
		$this->helper = $helper;
		
		/**
		 * Set message manager 
		 * 
		 * @var \Magento\Framework\Message\ManagerInterface $messageManager
		 */
		$this->messageManager = $messageManager;
	}
	
	/**
	 * After place plugin 
	 * 
	 * @param \Magento\Sales\Model\Service\OrderService $context
	 * @param \Magento\Sales\Model\Order $order
	 * @return \Magento\Sales\Model\Order
	 */
	public function afterPlace(\Magento\Sales\Model\Service\OrderService $context, \Magento\Sales\Model\Order $order)
	{
		if ($this->state->getAreaCode() === \Magento\Framework\App\Area::AREA_ADMINHTML && 1 === (int) $this->helper->getConfig('ec/gmp/use_measurement_protocol'))
		{
			if (false !== $data = $this->protocol->purchaseById
			(
				$order->getId()
			))
			{
				if (!$data->getErrors())
				{
					$this->messageManager->addNoticeMessage("Transaction data (ID:{$order->getIncrementId()}) sent successfully to Google Analytics (UA:{$data->getUA()})");
				}
				else 
				{
					foreach ($data->getErrors() as $error)
					{
						$this->messageManager->addErrorMessage($error);
					}
				}
			}
		}
		
		return $order;
	}
}