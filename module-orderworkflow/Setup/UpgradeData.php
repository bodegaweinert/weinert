<?php

namespace Combinatoria\OrderWorkflow\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\DB\Ddl\Table as DBDdlTable;

/**
 * Class UpgradeData
 * @package Combinatoria\OrderWorkflow\Setup
 */
class UpgradeData implements UpgradeDataInterface{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ConfigInterface
     */
    private $configInterface;

    /**
     * Status Factory
     *
     * @var StatusFactory
     */
    protected $statusFactory;

    /**
     * Status Resource Factory
     *
     * @var StatusResourceFactory
     */
    protected $statusResourceFactory;

    /**
     * @param ConfigInterface $configInterface
     * @param StatusFactory $statusFactory
     * @param StatusResourceFactory $statusResourceFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ConfigInterface $configInterface,
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->configInterface = $configInterface;
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
        $this->scopeConfig = $scopeConfig;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(),'1.0.1','<')){
            $this->_setDefaultConfiguration();
            $this->_disableOrderConfirmationEmail();
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')){
            $this->_updateDefaultStatusLabels($setup);
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')){
            $this->_addPaymentState();
            $this->_updateDefaultConfiguration();
            $this->_deleteOtherStatus($setup);
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')){
            $this->_fixPendingPaymentStatus($setup);
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')){
            $this->_checkMagentoOrderCancellationCron();
        }

        if (version_compare($context->getVersion(), '1.0.6','<')){
            $this->addAttributeOrderAvoidEmails($setup);
        }

        if (version_compare($context->getVersion(), '1.0.7','<')){
            $this->addAttributeOrderAvoidCreateCustomer($setup);
        }

        $setup->endSetup();
    }

    private function _updateDefaultStatusLabels(ModuleDataSetupInterface $setup){
        $setup->run('UPDATE `sales_order_status` SET `label`="Pendiente de Pago" WHERE `status` = "pending_payment";');

        $setup->run('UPDATE `sales_order_status` SET `label`="Completo" WHERE `status` = "complete";');

        $setup->run('UPDATE `sales_order_status` SET `label`="Cancelado" WHERE `status` = "canceled";');

        $setup->run('UPDATE `sales_order_status` SET `label`="Pendiente" WHERE `status` = "pending";');
    }

    private function _setDefaultConfiguration(){
        $path = 'cmb_ow/states_customization/state_new';
        $value = '1';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );

        $path = 'cmb_ow/states_customization/state_pending_payment';
        $value = '1';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );

        $path = 'cmb_ow/states_customization/state_payment_review';
        $value = '1';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );

        $path = 'cmb_ow/states_customization/state_invoiced';
        $value = '1';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );

        $path = 'cmb_ow/states_customization/state_shipping';
        $value = '1';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );

        $path = 'cmb_ow/states_customization/state_complete';
        $value = '1';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );
    }

    private function _disableOrderConfirmationEmail(){
        $path = 'sales_email/order/enabled';
        $value = '0';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );

    }


    private function _addPaymentState(){

        $stateStatuses = array(
            'payment_accredited' => array(
                'payment_accredited' => 'Pago Recibido'
            )
        );

        foreach ($stateStatuses as $state => $stateStatus) {

            foreach ($stateStatus as $statusCode => $statusLabel) {
                /** @var StatusResource $statusResource */
                $statusResource = $this->statusResourceFactory->create();
                /** @var Status $status */
                $status = $this->statusFactory->create();

                $status->setData([
                    'status' => $statusCode,
                    'label' => $statusLabel,
                ]);

                try {
                    $statusResource->save($status);
                } catch (AlreadyExistsException $exception) {

                    return;
                }

                $status->assignState($state, false, true);
            }
        }
    }

    private function _updateDefaultConfiguration(){
        $path = 'cmb_ow/states_customization/state_payment_accredited';
        $value = '1';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        );
    }


    private function _deleteOtherStatus(ModuleDataSetupInterface $setup){
        $setup->run("DELETE FROM `sales_order_status_state` WHERE `status` = 'closed'         AND `state` = 'closed';");
        $setup->run("DELETE FROM `sales_order_status_state` WHERE `status` = 'fraud'          AND `state` = 'payment_review';");
        $setup->run("DELETE FROM `sales_order_status_state` WHERE `status` = 'fraud'          AND `state` = 'processing';");
        $setup->run("DELETE FROM `sales_order_status_state` WHERE `status` = 'holded'         AND `state` = 'holded';");
        $setup->run("DELETE FROM `sales_order_status_state` WHERE `status` = 'payment_review' AND `state` = 'payment_review';");
        $setup->run("DELETE FROM `sales_order_status_state` WHERE `status` = 'processing'     AND `state` = 'processing';");

        $setup->run("DELETE FROM `sales_order_status` WHERE `status` = 'closed';");
        $setup->run("DELETE FROM `sales_order_status` WHERE `status` = 'fraud';");
        $setup->run("DELETE FROM `sales_order_status` WHERE `status` = 'holded';");
        $setup->run("DELETE FROM `sales_order_status` WHERE `status` = 'payment_review';");
        $setup->run("DELETE FROM `sales_order_status` WHERE `status` = 'paypal_canceled';");
        $setup->run("DELETE FROM `sales_order_status` WHERE `status` = 'paypal_reversed';");
        $setup->run("DELETE FROM `sales_order_status` WHERE `status` = 'pending_paypal';");
        $setup->run("DELETE FROM `sales_order_status` WHERE `status` = 'processing';");
        $setup->run("DELETE FROM `sales_order_status` WHERE `status` = 'paypal_canceled_reversal';");
    }

    private function _fixPendingPaymentStatus(ModuleDataSetupInterface $setup){
        $setup->run("UPDATE `sales_order_status_state` SET `visible_on_front` = 1 WHERE `status` = 'pending_payment';");
        return;
    }

    private function _checkMagentoOrderCancellationCron(){
        $path = 'sales/orders/delete_pending_after';
        $value = '10080';

        if ($this->_getConfigValue($path, \Magento\Store\Model\Store::DEFAULT_STORE_ID) != $value) {
            $this->configInterface->saveConfig(
                $path,
                $value,
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            );
        }
    }

    protected function _getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    private function addAttributeOrderAvoidEmails(ModuleDataSetupInterface $setup){

        /* quote and sales attributes columns definitions */

        $columns = [
            'avoid_emails' => [
                'type'      => DBDdlTable::TYPE_INTEGER,
                'nullable'  => true,
                'default'   => 0,
                'length'    => 11,
                'comment'   => 'avoid emails',
            ]
        ];

        /* tables where columns needs to be added */

        $tableNames = [
            'quote',
            'sales_order'
        ];

        $connection = $setup->getConnection();
        foreach ($tableNames as $tableName) {
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }

    }

    private function addAttributeOrderAvoidCreateCustomer(ModuleDataSetupInterface $setup){

        /* quote and sales attributes columns definitions */

        $columns = [
            'avoid_create_customer' => [
                'type'      => DBDdlTable::TYPE_INTEGER,
                'nullable'  => true,
                'default'   => 0,
                'length'    => 11,
                'comment'   => 'avoid create customer',
            ]
        ];

        /* tables where columns needs to be added */

        $tableNames = [
            'quote',
            'sales_order'
        ];

        $connection = $setup->getConnection();
        foreach ($tableNames as $tableName) {
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }

    }
}
