<?php
/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
 
namespace Sm\SocialLogin\Model\System\Config\Source;

class Effect implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
			['value' => 'mfp-move-from-top', 'label' => __('Move from top')],
            ['value' => 'mfp-zoom-in', 'label' => __('Zoom')],
			['value' => 'mfp-zoom-out', 'label' => __('Zoom-out')],
            ['value' => 'mfp-newspaper', 'label' => __('Newspaper')],
            ['value' => 'mfp-move-horizontal', 'label' => __('Horizontal move')],
            ['value' => 'mfp-3d-unfold', 'label' => __('3D unfold')]
        ];
    }
}
