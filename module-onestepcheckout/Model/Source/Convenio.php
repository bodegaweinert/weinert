<?php

namespace Aheadworks\OneStepCheckout\Model\Source;

class Convenio extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Entity attribute factory
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory
     */
    protected $entityAttributeFactory;

    /**
     * Eav resource helper
     *
     * @var \Magento\Eav\Model\ResourceModel\Helper
     */
    protected $eavResourceHelper;

    /**
     * Construct
     *
     * @param \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $entityAttributeFactory
     * @param \Magento\Eav\Model\ResourceModel\Helper $eavResourceHelper
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $entityAttributeFactory,
        \Magento\Eav\Model\ResourceModel\Helper $eavResourceHelper
    ) {
        $this->entityAttributeFactory = $entityAttributeFactory;
        $this->eavResourceHelper = $eavResourceHelper;
    }

    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __(''), 'value' => ''],
                ['label' => __('Local Inscripto'), 'value' => 'local_inscripto'],
                ['label' => __('Multilateral Inscripto'), 'value' => 'multilateral_inscripto'],
                ['label' => __('Local No Incripto'), 'value' => 'local_no_inscripto'],
                ['label' => __('Multilateral No Inscripto'), 'value' => 'multilateral_no_inscripto'],
            ];
        }
        return $this->_options;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeType = $this->getAttribute()->getBackendType();
        $attributeCode = $this->getAttribute()->getAttributeCode();

        return [
            $attributeCode => [
                'unsigned' => false,
                'default' => null,
                'extra' => null,
                'type' => $this->eavResourceHelper->getDdlTypeByColumnType($attributeType),
                'nullable' => true,
            ],
        ];
    }

    /**
     * Retrieve select for flat attribute update
     *
     * @param int $store
     * @return \Magento\Framework\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->entityAttributeFactory->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}