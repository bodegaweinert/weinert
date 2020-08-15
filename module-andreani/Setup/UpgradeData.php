<?php

namespace Ids\Andreani\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;

/**
 * Class UpgradeData
 *
 * @description Actualizador de datos para las tablas
 * @author Mauro Maximiliano Martinez <mmartinez@ids.net.ar>
 * @package Ids\Andreani\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * Status Factory
     *
     * @var StatusFactory
     */
    protected $statusFactory;

    /**
     * Status Resource Factory
     *
     * @var StatusResourceFactory
     */
    protected $statusResourceFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory
    )
    {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.2', '<'))
        {
            $eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY,'volumen','apply_to','simple');
        }


        if (version_compare($context->getVersion(), '1.0.4', '<'))
        {
            $stateStatuses = array(
                'shipping' => array(
                    'siniestro_andreani' => 'Siniestro',
                    'rechazado_andreani' => 'Rechazado'
                )
            );

            foreach ($stateStatuses as $state => $stateStatus) {

                foreach ($stateStatus as $statusCode => $statusLabel) {
                    /** @var StatusResource $statusResource */
                    $statusResource = $this->statusResourceFactory->create();
                    /** @var Status $status */
                    $status = $this->statusFactory->create();

                    $status->setData([
                        'status' => $statusCode,
                        'label' => $statusLabel,
                    ]);

                    try {
                        $statusResource->save($status);
                    } catch (AlreadyExistsException $exception) {

                        return;
                    }

                    $status->assignState($state, false, true);
                }
            }
        }

        $setup->endSetup();
    }
}
