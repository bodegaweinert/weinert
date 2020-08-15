<?php
namespace Combinatoria\Banner\Setup;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable('combinatoria_banner')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true ],
            'Banner ID'
        )->addColumn(
            'image',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ],
            'Image'
        )->addColumn(
            'alt',
            Table::TYPE_TEXT,
            null,
            [ 'nullable' => false ],
            'Alt'
        )->addColumn(
            'link',
            Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ],
            'Link'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            [ 'nullable' => false, 'default' => Table::TIMESTAMP_INIT ],
            'Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            [ 'nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE ],
            'Update Time'
        )->addColumn(
            'is_mobile',
            Table::TYPE_SMALLINT,
            null,
            [ 'nullable' => false ],
            'Is Mobile'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            [ 'nullable' => false ],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
