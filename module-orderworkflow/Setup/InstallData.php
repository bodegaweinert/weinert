<?php

namespace Combinatoria\OrderWorkflow\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Exception;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;


/**
 * Class InstallData
 */
class InstallData implements InstallDataInterface
{
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
     * InstallData constructor
     *
     * @param StatusFactory $statusFactory
     * @param StatusResourceFactory $statusResourceFactory
     */
    public function __construct(
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory
    ) {
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
    }


    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     *
     * @throws Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $stateStatuses = array(
            'invoiced' => array(
                'invoice_pending' => 'Pendiente de Factura',
                'invoiced'        => 'Facturado'
            ),
            'shipping' => array(
                'shipping_pending' => 'Pendiente de Envio',
                'delivered'        => 'Despachado',
                'received'         => 'Recibido'
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

        $setup->endSetup();
    }
}