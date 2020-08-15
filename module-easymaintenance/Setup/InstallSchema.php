<?php
/**
 * Copyright © 2015 Biztech. All rights reserved.
 */

namespace Biztech\Easymaintenance\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	
        $installer = $setup;

        $installer->startSetup();

		/**
         * Create table 'easymaintenance_sitenotify'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('easymaintenance_sitenotify')
        )
		->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'bc_easymaintenance_sitenotify'
        )
		->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Name'
        )
		->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Email'
        )

        ->setComment(
            'Biztech Easymaintenance easymaintenance_sitenotify'
        );
		
		$installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
