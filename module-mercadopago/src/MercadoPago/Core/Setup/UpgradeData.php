<?php

namespace MercadoPago\Core\Setup;

use Magento\Customer\Model\Metadata\AddressMetadata;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table as DBDdlTable;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;

class UpgradeData implements UpgradeDataInterface{

    /**
     * @var ConfigInterface
     */
    private $configInterface;

    /**
     * @param ConfigInterface $configInterface
     */
    public function __construct(
        ConfigInterface $configInterface
    ) {
        $this->configInterface = $configInterface;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(),'1.5.4','<')){

            $this->updateConfig();
        }

        $setup->endSetup();
    }

    private function updateConfig(){
        $path = 'payment/mercadopago_custom/title';
        $value = 'Tarjeta de crédito';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $path = 'payment/mercadopago_customticket/title';
        $value = 'Ticket impreso';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID);
    }
}
