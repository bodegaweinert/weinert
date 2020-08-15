<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


namespace Amasty\XmlSitemap\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->_addDateFormatColumn($setup);
        }

        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $this->_addProductStatusColumn($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function _addDateFormatColumn(SchemaSetupInterface $setup)
    {
        $setup->getConnection()
            ->addColumn(
                $setup->getTable('amasty_xml_sitemap'),
                'date_format',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 20,
                    'nullable' => false,
                    'comment' => 'Date Format'
                ]
            );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function _addProductStatusColumn(SchemaSetupInterface $setup)
    {
        $setup->getConnection()
            ->addColumn(
                $setup->getTable('amasty_xml_sitemap'),
                'exclude_out_of_stock',
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'comment' => 'Exclude Out Of Stock Products'
                ]
            );
    }
}
