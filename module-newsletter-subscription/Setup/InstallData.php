<?php

namespace Combinatoria\NewsletterSubscription\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class InstallData implements InstallDataInterface
{
    /**
     * @var ScopeConfigInterface $scopeConfig
     */
    private $scopeConfig;

    /**
     * @var ConfigInterface $configInterface
     */
    private $configInterface;


    /**
     * Class Constructor
     *
     * @param ConfigInterface $configInterface
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ConfigInterface $configInterface,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->configInterface = $configInterface;
        $this->scopeConfig = $scopeConfig;
    }


    /**
     * Create custom field in subscribers table
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Create Contabilium ID for sales entity
         */

        $newsletterTable = $setup->getTable('newsletter_subscriber');
        $columns = [
            'coupon_code'  => [
                'type'     => Table::TYPE_TEXT,
                'length'   => 50,
                'nullable' => true,
                'comment'  => 'Coupon Code',
            ]
        ];

        $connection = $setup->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($newsletterTable, $name, $definition);
        }

        /**
         * Sets the default config
         */

        $path = 'newsletter/coupon/code_length';
        $value = $this->scopeConfig->getValue('promo/auto_generated_coupon_codes/length', ScopeInterface::SCOPE_STORE);
        $this->configInterface->saveConfig($path, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, Store::DEFAULT_STORE_ID);

        $path = 'newsletter/coupon/code_format';
        $value = $this->scopeConfig->getValue('promo/auto_generated_coupon_codes/format', ScopeInterface::SCOPE_STORE);
        $this->configInterface->saveConfig($path, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, Store::DEFAULT_STORE_ID);

        $path = 'newsletter/coupon/code_prefix';
        $value = $this->scopeConfig->getValue('promo/auto_generated_coupon_codes/prefix', ScopeInterface::SCOPE_STORE);
        $this->configInterface->saveConfig($path, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, Store::DEFAULT_STORE_ID);

        $path = 'newsletter/coupon/code_suffix';
        $value = $this->scopeConfig->getValue('promo/auto_generated_coupon_codes/suffix', ScopeInterface::SCOPE_STORE);
        $this->configInterface->saveConfig($path, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, Store::DEFAULT_STORE_ID);

        $path = 'newsletter/coupon/dash_every_x_characters';
        $value = $this->scopeConfig->getValue('promo/auto_generated_coupon_codes/dash', ScopeInterface::SCOPE_STORE);
        if ($value == '') $value = 0;
        $this->configInterface->saveConfig($path, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, Store::DEFAULT_STORE_ID);

    }

}