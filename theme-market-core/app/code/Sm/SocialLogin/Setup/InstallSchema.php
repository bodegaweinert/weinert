<?php
/**
 *
 * SM Social Login - Version 1.0.0
 * Copyright (c) 2017 YouTech Company. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: YouTech Company
 * Websites: http://www.magentech.com
 */
 
namespace Sm\SocialLogin\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('sm_social_customer')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('sm_social_customer')
            )
                ->addColumn(
                    'social_customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 11,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Social Customer ID'
                )
                ->addColumn(
                    'social_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['unsigned' => true, 'nullable => false'], 'Social Id'
                )->addColumn(
                    'customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['unsigned' => true, 'nullable => false'], 'Customer Id'
                )->addColumn(
                    'is_send_password_email', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, ['unsigned' => true, 'nullable => false', 'default' => '0'], 'Is Send Password Email'
                )->addColumn(
                    'type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['default' => ''], 'Type'
                )->addForeignKey(
                    $installer->getFkName('sm_social_customer', 'customer_id', 'customer_entity', 'entity_id'),
                    'customer_id',
                    $installer->getTable('customer_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->setComment('Social Customer Table');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
