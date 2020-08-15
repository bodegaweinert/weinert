<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */


namespace Amasty\Rules\Setup;

use Amasty\Rules\Model\CheckEnterprise;
use Magento\Framework\App\State;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;


class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var CheckEnterprise
     */
    private $checkEnterprise;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var Operation\AddAmrulesTable
     */
    private $addAmrulesTable;

    /**
     * @var Operation\MigrateRules
     */
    private $migrateRules;

    /**
     * @var Operation\ScheduleRules
     */
    private $scheduleRules;

    public function __construct(
        State $appState,
        CheckEnterprise $checkEnterprise,
        Operation\AddAmrulesTable\Proxy $addAmrulesTable,
        Operation\MigrateRules\Proxy $migrateRules,
        Operation\ScheduleRules\Proxy $scheduleRules
    ) {
        $this->appState = $appState;
        $this->addAmrulesTable = $addAmrulesTable;
        $this->migrateRules = $migrateRules;
        $this->scheduleRules = $scheduleRules;
        $this->checkEnterprise = $checkEnterprise;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addAmrulesTable->execute($setup);
        }

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->appState->emulateAreaCode(
                \Magento\Framework\App\Area::AREA_FRONTEND,
                [$this->migrateRules, 'execute']
            );
        }

        if (version_compare($context->getVersion(), '2.1.1', '<')
            && $this->checkEnterprise->isEnterprise()
        ) {
            $this->scheduleRules->execute($setup);
        }

        if (version_compare($context->getVersion(), '2.2.3', '<')) {
            $this->addApplyDiscountTo($setup);
        }

        if (version_compare($context->getVersion(), '2.2.4', '<')) {
            $this->addUseFor($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addApplyDiscountTo(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_amrules_rule'),
            'apply_discount_to',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length'   => 4,
                'nullable' => false,
                'comment'  => 'Apply Discount To'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addUseFor(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_amrules_rule'),
            'use_for',
            [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length'   => 4,
                'nullable' => false,
                'comment'  => 'Use'
            ]
        );
    }

}
