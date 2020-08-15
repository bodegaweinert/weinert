<?php
/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */

namespace Sm\SocialLogin\Block\Form;

class Login extends \Magento\Customer\Block\Form\Login
{
	protected function _prepareLayout()
	{
		return $this;
	}
}
