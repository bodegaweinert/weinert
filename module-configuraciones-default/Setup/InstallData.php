<?php

namespace Combinatoria\ConfiguracionesDefault\Setup;

use Magento\Directory\Helper\Data;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;


class InstallData implements InstallDataInterface
{

    /**
     * Directory data
     *
     * @var Data
     */
    protected $directoryData;

    private $configInterface;
    /**
     * Init
     *
     * @param Data $directoryData
     */
    public function __construct(Data $directoryData,ConfigInterface $configInterface)
    {
        $this->directoryData = $directoryData;
        $this->configInterface = $configInterface;
    }


    /**
     * Install Data
     *
     * @param ModuleDataSetupInterface $setup   Module Data Setup
     * @param ModuleContextInterface   $context Module Context
     *
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $configurations = [
            ["carriers/flatrate/title","Envío a Domicilio"],
            ["carriers/flatrate/name","Cargo fijo"],
            ["catalog/frontend/grid_per_page","15"],
            ["multishipping/options/checkout_multiple","0"],
            ["checkout/cart/configurable_product_image","itself"],
            ["aw_osc/general/title","Completá tus datos"],
            ["payment/checkmo/active","0"]
        ];

        foreach ($configurations as $item){

            $path = $item[0];
            $value = $item[1];
            $this->configInterface->saveConfig(
                $path,
                $value,
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID);
        }

    }

}

