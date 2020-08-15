<?php
/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
 
namespace Sm\SocialLogin\Block\System;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field as FormField;
use Sm\SocialLogin\Helper\Social as SocialHelper;
use Magento\Backend\Block\Template\Context;

class RedirectUrl extends FormField
{
	protected $socialHelper;

	public function __construct(
		Context $context,
		SocialHelper $socialHelper,
		array $data = []
	)
	{
		$this->socialHelper = $socialHelper;
		parent::__construct($context, $data);
	}

	protected function _getElementHtml(AbstractElement $element)
	{
		$elementId   = explode('_', $element->getHtmlId());
		$redirectUrl = $this->socialHelper->getAuthUrl($elementId[1]);
		$html        = '<input style="opacity:1;" readonly id="' . $element->getHtmlId() . '" class="input-text admin__control-text" value="' . $redirectUrl . '" onclick="this.select()" type="text">';

		return $html;
	}
}
