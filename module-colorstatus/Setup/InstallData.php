<?php

/**
 * Color Status
 *
 * Set the each sales row with a background color on sales grid.
 *
 * @package Igorludgero\Colorstatus
 * @author Igor Ludgero Miura <igor@imaginemage.com>
 * @copyright Copyright (c) 2017 Igor Ludgero Miura (https://www.igorludgero.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

namespace Igorludgero\Colorstatus\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('sales_order_status');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) == true){
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_status'),
                'color',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Color Column'
                ]
            );
        }
    }
}