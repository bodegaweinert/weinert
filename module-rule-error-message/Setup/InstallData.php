<?php
namespace Combinatoria\RuleErrorMessage\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallData implements InstallDataInterface
{
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
         * Create error_message for sales and catalog rules
         */
        $columns = [
            'error_message'  => [
                'type'     => Table::TYPE_TEXT,
                'length'   => 500,
                'nullable' => true,
                'comment'  => 'Error Message',
            ]
        ];

        $tables = [
            'salesrule'
        ];

        $connection = $setup->getConnection();
        foreach ($tables as $table){
            foreach ($columns as $name => $definition) {
                $connection->addColumn($table, $name, $definition);
            }
        }
    }
}