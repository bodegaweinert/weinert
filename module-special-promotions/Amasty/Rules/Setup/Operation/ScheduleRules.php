<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


namespace Amasty\Rules\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;

class ScheduleRules
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $adapter */
        $adapter = $setup->getConnection();

        $adapter->dropForeignKey(
            $setup->getTable('amasty_amrules_rule'),
            $setup->getFkName(
                'amasty_amrules_rule',
                'salesrule_id',
                'salesrule',
                'rule_id'
            )
        );

        $adapter->addForeignKey(
            $setup->getFkName(
                'amasty_amrules_rule',
                'salesrule_id',
                'salesrule',
                'row_id'
            ),
            $setup->getTable('amasty_amrules_rule'),
            'salesrule_id',
            $setup->getTable('salesrule'),
            'row_id'
        );
    }
}
