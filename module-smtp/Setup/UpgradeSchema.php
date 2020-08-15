<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Smtp
 * @copyright   Copyright (c) 2017 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Smtp\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Mageplaza\Smtp\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $setup->getConnection()
                ->addColumn($setup->getTable('mageplaza_smtp_log'), 'from', [
                        'type'     => Table::TYPE_TEXT,
                        'nullable' => true,
                        'length'   => 255,
                        'comment'  => 'Sender'
                    ]
                );
            $setup->getConnection()
                ->addColumn($setup->getTable('mageplaza_smtp_log'), 'to', [
                        'type'     => Table::TYPE_TEXT,
                        'nullable' => true,
                        'length'   => 255,
                        'comment'  => 'Recipient'
                    ]
                );
            $setup->getConnection()
                ->addColumn($setup->getTable('mageplaza_smtp_log'), 'cc', [
                        'type'     => Table::TYPE_TEXT,
                        'nullable' => true,
                        'length'   => 255,
                        'comment'  => 'Cc'
                    ]
                );
            $setup->getConnection()
                ->addColumn($setup->getTable('mageplaza_smtp_log'), 'bcc', [
                        'type'     => Table::TYPE_TEXT,
                        'nullable' => true,
                        'length'   => 255,
                        'comment'  => 'Bcc'
                    ]
                );
        }
        if (version_compare($context->getVersion(), '1.1.1','<')){
            $this->_generateQueueTables($setup);
        }

        if (version_compare($context->getVersion(),'1.1.2','<')){
            $setup->getConnection()
                ->addColumn($setup->getTable('smtp_queue'), 'cc', [
                        'type'     => Table::TYPE_TEXT,
                        'nullable' => true,
                        'length'   => 500,
                        'comment'  => 'Cc'
                    ]
                );
            $setup->getConnection()
                ->addColumn($setup->getTable('smtp_queue'), 'bcc', [
                        'type'     => Table::TYPE_TEXT,
                        'nullable' => true,
                        'length'   => 500,
                        'comment'  => 'Bcc'
                    ]
                );
        }

        $setup->endSetup();
    }

    private function _generateQueueTables(SchemaSetupInterface $setup){
        $table = $setup->getConnection()->newTable(
            $setup->getTable('smtp_queue')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true ],
            'Queue Id'
        )->addColumn(
            'template_xml_path',
            Table::TYPE_TEXT,
            250,
            [ 'nullable' => false ],
            'TemplateXmlPath'
        )->addColumn(
            'order_id',
            Table::TYPE_INTEGER,
            null,
            [ 'nullable' => true ],
            'Order Id'
        )->addColumn(
            'store_id',
            Table::TYPE_INTEGER,
            null,
            [ 'nullable' => false ],
            'Store Id'
        )->addColumn(
            'variables',
            Table::TYPE_TEXT,
            5000,
            [ 'nullable' => false ],
            'Variables'
        )->addColumn(
            'sender_info',
            Table::TYPE_TEXT,
            500,
            [ 'nullable' => false ],
            'Sender Info'
        )->addColumn(
            'receiver_info',
            Table::TYPE_TEXT,
            500,
            [ 'nullable' => false ],
            'Receiver Info'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            [ 'nullable' => false, 'default' => Table::TIMESTAMP_INIT ],
            'Creation Time'
        )->addColumn(
            'sent_at',
            Table::TYPE_TIMESTAMP,
            null,
            [ 'nullable' => true],
            'Sent at time'
        )->addColumn(
            'email_sent',
            Table::TYPE_SMALLINT,
            null,
            [ 'nullable' => false, 'default' => 0],
            'Email Sent'
        );
        $setup->getConnection()->createTable($table);
    }
}