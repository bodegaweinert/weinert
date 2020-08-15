<?php

namespace MercadoPago\Core\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema
    implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        $invoiceTable = $installer->getTable('sales_invoice');
        $creditMemoTable = $installer->getTable('sales_creditmemo');
        $salesTable = $installer->getTable('sales_order');
        $quoteTable = $installer->getTable('quote');


        /*********** VERSION 1.0.2 ADD FINANCE COST COLUMNS TO INVOICE AND CREDITMEMO ***********/
        if (version_compare($context->getVersion(), '1.0.2', '<=')) {

            $columns = [
                'finance_cost_amount'      => [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'   => '12,4',
                    'nullable' => true,
                    'comment'  => 'Finance Cost Amount',
                ],
                'base_finance_cost_amount' => [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'   => '12,4',
                    'nullable' => true,
                    'comment'  => 'Base Finance Cost Amount',
                ]
            ];

            foreach ($columns as $name => $definition) {
                $connection->addColumn($invoiceTable, $name, $definition);
                $connection->addColumn($creditMemoTable, $name, $definition);
            }
        }

        /*********** VERSION 1.0.3 ADD DISCOUNT COUPON COLUMNS TO ORDER, QUOTE, INVOICE, CREDITMEMO ***********/
        if (version_compare($context->getVersion(), '1.0.3', '<=')) {
            $columns = [
                'discount_coupon_amount'      => [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'   => '12,4',
                    'nullable' => true,
                    'comment'  => 'Discount coupon Amount',
                ],
                'base_discount_coupon_amount' => [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length'   => '12,4',
                    'nullable' => true,
                    'comment'  => 'Base Discount coupon Amount',
                ]
            ];

            foreach ($columns as $name => $definition) {
                $connection->addColumn($invoiceTable, $name, $definition);
                $connection->addColumn($creditMemoTable, $name, $definition);
                $connection->addColumn($salesTable, $name, $definition);
                $connection->addColumn($quoteTable, $name, $definition);
            }

        }


        /*********** VERSION 1.0.4 ADD DISCOUNT COUPON COLUMNS TO QUOTE_ADDRESS ***********/

        if (version_compare($context->getVersion(), '1.0.4', '<=')) {
            $quoteAddressTable = $installer->getTable('quote_address');
            $columns = ['discount_coupon_amount'      => ['type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                                                          'length'   => '12,4',
                                                          'nullable' => true,
                                                          'comment'  => 'Discount coupon Amount',],
                        'base_discount_coupon_amount' => ['type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                                                          'length'   => '12,4',
                                                          'nullable' => true,
                                                          'comment'  => 'Base Discount coupon Amount',]];

            foreach ($columns as $name => $definition) {
                $connection->addColumn($quoteAddressTable, $name, $definition);
            }
        }

        $setup->endSetup();

        /*********** VERSION 1.0.5 ADD FINANCING COST COLUMN TO QUOTE_ADDRESS ***********/

        if (version_compare($context->getVersion(), '1.0.5', '<=')) {
            $quoteAddressTable = $installer->getTable('quote_address');
            $columns = ['finance_cost_amount'      => ['type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                                                          'length'   => '12,4',
                                                          'nullable' => true,
                                                          'comment'  => 'Finance Cost Amount'],
                        'base_finance_cost_amount' => ['type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                                                          'length'   => '12,4',
                                                          'nullable' => true,
                                                          'comment'  => 'Base Finance Cost Amount']];

            foreach ($columns as $name => $definition) {
                $connection->addColumn($quoteAddressTable, $name, $definition);
            }
        }

        /*********** VERSION 1.5.5 ADD NOTIFICATION TABLE ***********/

        if (version_compare($context->getVersion(), '1.5.5', '<=')) {
            /**
             * Create notification table
             */
            if (!$installer->tableExists('mercadopago_core_notification')) {

                $table = $installer->getConnection()
                    ->newTable($installer->getTable('mercadopago_core_notification'))
                    ->addColumn('id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary'  => true, 'unsigned' => true], 'Log ID')
                    ->addColumn('order_id', Table::TYPE_BIGINT, 11, ['nullable' => false], 'Order Id')
                    ->addColumn('mp_payment_id', Table::TYPE_TEXT, 255, ['nullable' => false], 'MP Payment ID')
                    ->addColumn('notification', Table::TYPE_TEXT, 500, ['nullable' => false], 'Notification')
                    ->addColumn('status', Table::TYPE_TEXT, 100, ['nullable' => false], 'Status')
                    ->addColumn('applied', Table::TYPE_INTEGER, 1, ['nullable' => false], 'Applied')
                    ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Created At')
                    ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, [], 'Updated At');

                $installer->getConnection()->createTable($table);
            }
        }

        $setup->endSetup();
    }
}
