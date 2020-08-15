<?php
/*------------------------------------------------------------------------
# SM Market - Version 1.0.0
# Copyright (c) 2016 YouTech Company. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: YouTech Company
# Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

namespace Sm\Market\Model\Config\Source;

class ProductSource implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'lastest_products', 'label' => __('Lastest products')],
            ['value' => 'best_sellers', 'label' => __('Best sellers')],
            ['value' => 'special_products', 'label' => __('Special products')],
            ['value' => 'featured_products', 'label' => __('Featured products')],
            ['value' => 'other_products', 'label' => __('Other products')],
            ['value' => 'viewed_products', 'label' => __('Viewed products')],
            ['value' => 'countdown_products', 'label' => __('Countdown products')],
            ['value' => 'category_position', 'label' => __('Category position')]
        ];
    }
}

