<?php
/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
 
namespace Sm\SocialLogin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Social extends AbstractDb
{
	protected function _construct()
	{
		$this->_init('sm_social_customer', 'social_customer_id');
	}
}