<?php

namespace Aheadworks\OneStepCheckout\Ui\Component\Listing\Column\SalesOrder\RequireInvoice;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            $this->options[] = ['value' => 1,'label' => __("Yes")];
            $this->options[] = ['value' => 0,'label' => __("No")];
        }

        return $this->options;
    }
}
