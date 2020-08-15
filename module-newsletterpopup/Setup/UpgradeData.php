<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Plumrocket\Newsletterpopup\Model\TemplateFactory
     */
    private $templateFactory;

    /**
     * @var \Plumrocket\Newsletterpopup\Helper\TemplateFactory
     */
    private $helperTemplateFactory;

    /**
     * UpgradeData constructor.
     *
     * @param \Plumrocket\Newsletterpopup\Model\TemplateFactory  $templateFactory
     * @param \Plumrocket\Newsletterpopup\Helper\TemplateFactory $helperTemplateFactory
     */
    public function __construct(
        \Plumrocket\Newsletterpopup\Model\TemplateFactory $templateFactory,
        \Plumrocket\Newsletterpopup\Helper\TemplateFactory $helperTemplateFactory
    ) {
        $this->templateFactory = $templateFactory;
        $this->helperTemplateFactory = $helperTemplateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '3.0.3', '<')) {
            // Update all templates (cleared from h-tags)
            $rows = $this->helperTemplateFactory->create()->getAllData();

            foreach ($rows as $row) {
                $this->templateFactory
                    ->create()
                    ->setData($row)
                    ->setCanSaveBaseTemplates(true)
                    ->save();
            }
        }

        /**
         * Version 3.1.0
         */
        if (version_compare($context->getVersion(), '3.1.0', '<')) {
            $tableName = $setup->getTable('plumrocket_newsletterpopup_templates');

            /**
             * Update Thank you message for all templates
             */
            if ($connection->isTableExists($tableName) == true) {
                $connection->update(
                    $tableName,
                    [
                        'default_values' => new \Zend_Db_Expr('REPLACE(`default_values`, \'Thank you for your subscription.\', \'Thank you for your subscription!\')'),
                    ]
                );
            }

            /**
             * Update Fireworks Template
             */
            $templateModel = $this->templateFactory->create()->load(12);

            if ($templateModel && $templateModel->getId()) {
                $defaultValues = unserialize($templateModel->getData('default_values'));
                $defaultValues['text_success'] = '<p><strong style="font-size: 28px; line-height: 33px;">Enjoy 15% OFF Your Entire Purchase.</strong></p>'
                    . '<p style="padding-top: 15px;">Enter Coupon Code:&nbsp;<strong style="color: #ca0b0b; background: #faffad; padding: 5px 7px; border-radius: 3px; border: 1px dashed #d4da65;">{{coupon_code}}</strong><br/>At Checkout</p>'
                    . '<p style="padding-top: 24px; color: #d00000;">Hurry! This Offer Ends in 2 HOURS!</p>';

                $templateModel->setData('default_values', serialize($defaultValues))
                    ->setData('can_save_base_templates', true)
                    ->save();
            }

        }

        $setup->endSetup();
    }
}
