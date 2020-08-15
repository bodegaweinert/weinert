<?php
/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
 
namespace Sm\SocialLogin\Controller\Social;

class Callback extends \Magento\Framework\App\Action\Action
{
	public function execute()
	{
		\Hybrid_Endpoint::process();
	}
}