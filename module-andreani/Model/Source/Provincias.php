<?php

namespace Ids\Andreani\Model\Source;

use Ids\Andreani\Helper\Data as AndreaniHelper;

class Provincias implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var AndreaniHelper
     */
    protected $_andreaniHelper;

    public function __construct(
        AndreaniHelper $andreaniHelper
    ) {
        $this->_andreaniHelper = $andreaniHelper;
    }
    public function toOptionArray()
    {
        $helper             = $this->_andreaniHelper;
        $provinciasData     = $helper->getProvincias();
        $provincias         = [];

        foreach ($provinciasData AS $key=>$provinciaData)
        {
            $provincias[] = [
                'label' => $provinciaData['title'],
                'value' => $provinciaData['value'],
            ];
        }

        return $provincias;
    }
}
