<?php

namespace Aheadworks\OneStepCheckout\Setup;

use Magento\Customer\Model\Metadata\AddressMetadata;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table as DBDdlTable;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;

/**
 * Class UpgradeData
 * @package Aheadworks\OneStepCheckout\Setup
 */
class UpgradeData implements UpgradeDataInterface{

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var ConfigInterface
     */
    private $configInterface;

    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     * @param ConfigInterface $configInterface
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        ConfigInterface $configInterface
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->configInterface = $configInterface;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(),'1.2.0','<')){
            $this->addCustomAddressAttributes($setup);
            $this->updateAddressTemplateConfig($context);
            $this->updateExtensionConfig();
        }

        if (version_compare($context->getVersion(),'1.2.1','<')){
            $this->updateAddressTemplateConfig($context);
        }

        if (version_compare($context->getVersion(), '1.2.2','<')){
            $this->addBusinessAttributes($setup);
            $this->updateAddressTemplateConfigForInvoice($context);
        }

        if (version_compare($context->getVersion(), '1.2.3','<')){
            $this->addRequireInvoiceAttributeToGrid($setup);
        }

        if (version_compare($context->getVersion(), '1.2.4','<')){
            $this->addConvenio($setup);
        }

        if (version_compare($context->getVersion(), '1.2.5','<')){
            $this->addConvenioRates($setup);
        }

        if (version_compare($context->getVersion(),'1.2.6','<')){
            $this->addAddressDob($setup);
            $this->updateAddressTemplateDob($context);
        }

        $setup->endSetup();
    }

    private function addCustomAddressAttributes(ModuleDataSetupInterface $setup){

        /* customer address attributes definition */
        $attributesInfo = [
            'street_number' => [
                'label'         => 'Street Number',
                'type'          => 'text',
                'input'         => 'text',
                'position'      => 125,
                'visible'       => true,
                'system'        => false,
                'user_defined'  => false,
                'required'      => false,
            ],
            'street_floor' => [
                'label'         => 'Street Floor',
                'type'          => 'text',
                'input'         => 'text',
                'position'      => 126,
                'visible'       => true,
                'system'        => false,
                'user_defined'  => false,
                'required'      => false,
            ],
            'street_apartment' => [
                'label'         => 'Street Apartment',
                'type'          => 'text',
                'input'         => 'text',
                'position'      => 127,
                'visible'       => true,
                'system'        => false,
                'user_defined'  => false,
                'required'      => false,
            ],
            'dni'  => [
                'label'         => 'Dni',
                'type'          => 'text',
                'input'         => 'text',
                'position'      => 128,
                'visible'       => true,
                'system'        => false,
                'user_defined'  => false,
                'required'      => false,
            ]
        ];

        /* customer address attributes columns definitions */

        $columns = [
            'street_number' => [
                'type'      => DBDdlTable::TYPE_TEXT,
                'nullable'  => true,
                'default'   => null,
                'length'    => 255,
                'comment'   => 'Street Number',
            ],
            'street_floor' => [
                'type'      => DBDdlTable::TYPE_TEXT,
                'nullable'  => true,
                'default'   => null,
                'length'    => 255,
                'comment'   => 'Street Floor',
            ],
            'street_apartment' => [
                'type'      => DBDdlTable::TYPE_TEXT,
                'nullable'  => true,
                'default'   => null,
                'length'    => 255,
                'comment'   => 'Street Apartment',
            ],
            'dni' => [
                'type'      => DBDdlTable::TYPE_TEXT,
                'nullable'  => true,
                'default'   => null,
                'length'    => 255,
                'comment'   => 'Dni',
            ]
        ];

        /* tables where columns needs to be added */

        $tableNames = [
            'customer_address_entity',
            'quote_address',
            'sales_order_address'
        ];

        /* adding customer address attributes */

        /** @var $customerInstaller CustomerSetup */
        $customerInstaller = $this->customerSetupFactory->create(['setup' => $setup]);
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId(AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS);

        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $customerInstaller->addAttribute('customer_address', $attributeCode, $attributeParams);
            $customerInstaller->getEavConfig()->getAttribute(AddressMetadataInterface::ENTITY_TYPE_ADDRESS, $attributeCode)
                ->setData('attribute_set_id', AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS)
                ->setData('attribute_group_id', $attributeGroupId)
                ->setData('used_in_forms', [
                    'customer_address_edit',
                    'customer_register_address',
                    'adminhtml_customer_address',
                    'customer_address'
                ])
                ->save();
        }

        /* adding customer address attributes columns */

        $connection = $setup->getConnection();
        foreach ($tableNames as $tableName) {
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }

        /* adding customer attribute */

        $attributesInfo = [
            'customer_dni' => [
                'label'         => 'Customer Dni',
                'type'          => 'text',
                'input'         => 'text',
                'position'      => 125,
                'visible'       => true,
                'system'        => false,
                'user_defined'  => false,
                'required'      => false,
                'group'         => 'General Information'
            ]
        ];

        $attributeGroupId = $attributeSet->getDefaultGroupId(CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER);

        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $customerInstaller->addAttribute('customer', $attributeCode, $attributeParams);
            $customerInstaller->getEavConfig()->getAttribute(CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER, $attributeCode)
                ->setData('attribute_set_id', CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER)
                ->setData('attribute_group_id', $attributeGroupId)
                ->setData('used_in_forms', [
                    'adminhtml_customer',
                    'customer_account_create',
                    'customer_account_edit'
                ])
                ->save();
        }
    }

    private function updateAddressTemplateConfig(ModuleContextInterface $context){
        if (version_compare($context->getVersion(),'1.2.0','<')) {
            $path = 'customer/address_templates/oneline';
            $value = '{{var street}} {{var street_number}} {{var street_floor}} {{var street_apartment}}, CP: {{var postcode}}, {{var city}}, {{var region}}';

            $this->configInterface->saveConfig(
                $path,
                $value,
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID);
        }

        if (version_compare($context->getVersion(),'1.2.1','<')){
            $path = 'customer/address_templates/text';
            $value = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}
{{depend company}}{{var company}}{{/depend}}
{{if street1}}{{var street1}}
{{var street_number}} {{var street_floor}} {{var street_apartment}}
{{/if}}
{{depend street2}}{{var street2}}{{/depend}}
{{depend street3}}{{var street3}}{{/depend}}
{{depend street4}}{{var street4}}{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}
{{depend telephone}}T: {{var telephone}}{{/depend}}
{{depend fax}}F: {{var fax}}{{/depend}}
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}';
            $this->configInterface->saveConfig(
                $path,
                $value,
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID);

            $path = 'customer/address_templates/html';
            $value = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}{{depend firstname}}<br />{{/depend}}
{{depend company}}{{var company}}<br />{{/depend}}
{{if street1}}{{var street1}} {{var street_number}} {{var street_floor}} {{var street_apartment}}<br />{{/if}}
{{depend street2}}{{var street2}}<br />{{/depend}}
{{depend street3}}{{var street3}}<br />{{/depend}}
{{depend street4}}{{var street4}}<br />{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}<br />
{{depend telephone}}T: <a href="tel:{{var telephone}}">{{var telephone}}</a>{{/depend}}
{{depend fax}}<br />F: {{var fax}}{{/depend}}
{{depend vat_id}}<br />VAT: {{var vat_id}}{{/depend}}';
            $this->configInterface->saveConfig(
                $path,
                $value,
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID);

            $path = 'customer/address_templates/pdf';
            $value = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}|
{{depend company}}{{var company}}|{{/depend}}
{{if street1}}{{var street1}}|{{var street_number}}| {{var street_floor}}| {{var street_apartment}}|{{/if}}
{{depend street2}}{{var street2}}|{{/depend}}
{{depend street3}}{{var street3}}|{{/depend}}
{{depend street4}}{{var street4}}|{{/depend}}
{{if city}}{{var city}}, {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}|
{{depend telephone}}T: {{var telephone}}|{{/depend}}
{{depend fax}}F: {{var fax}}|{{/depend}}|
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}|';
            $this->configInterface->saveConfig(
                $path,
                $value,
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID);
        }

    }

    private function updateExtensionConfig(){
        $path = 'aw_osc/billing_customization/fields_customization';
        $value = 'a:2:{s:4:"rows";a:6:{s:14:"name-field-row";a:1:{s:10:"sort_order";s:1:"0";}s:24:"customer-extra-field-row";a:1:{s:10:"sort_order";s:1:"1";}s:17:"address-field-row";a:1:{s:10:"sort_order";s:1:"2";}s:14:"city-field-row";a:1:{s:10:"sort_order";s:1:"3";}s:23:"phone-company-field-row";a:1:{s:10:"sort_order";s:1:"4";}s:16:"vat_id-field-row";a:1:{s:10:"sort_order";s:1:"5";}}s:10:"attributes";a:18:{s:6:"prefix";a:3:{s:5:"label";s:11:"Name Prefix";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}s:9:"firstname";a:3:{s:5:"label";s:10:"First Name";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}s:10:"middlename";a:3:{s:5:"label";s:19:"Middle Name/Initial";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}s:8:"lastname";a:3:{s:5:"label";s:9:"Last Name";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}s:6:"suffix";a:3:{s:5:"label";s:11:"Name Suffix";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}s:9:"telephone";a:3:{s:5:"label";s:12:"Phone Number";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}s:3:"dni";a:3:{s:5:"label";s:3:"Dni";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}s:6:"street";a:2:{i:0;a:3:{s:5:"label";s:5:"Calle";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}i:1;a:3:{s:5:"label";s:21:"Street Address Line 2";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}}s:13:"street_number";a:3:{s:5:"label";s:7:"Número";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}s:12:"street_floor";a:3:{s:5:"label";s:4:"Piso";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";}s:16:"street_apartment";a:3:{s:5:"label";s:12:"Departamento";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";}s:4:"city";a:3:{s:5:"label";s:4:"City";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}s:10:"country_id";a:3:{s:5:"label";s:7:"Country";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";}s:6:"region";a:3:{s:5:"label";s:14:"State/Province";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";}s:8:"postcode";a:3:{s:5:"label";s:15:"Zip/Postal Code";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";}s:7:"company";a:3:{s:5:"label";s:7:"Company";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}s:3:"fax";a:3:{s:5:"label";s:3:"Fax";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}s:6:"vat_id";a:3:{s:5:"label";s:10:"VAT Number";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}}}';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $path = 'aw_osc/shipping_customization/fields_customization';
        $value = 'a:2:{s:4:"rows";a:6:{s:14:"name-field-row";a:1:{s:10:"sort_order";s:1:"0";}s:24:"customer-extra-field-row";a:1:{s:10:"sort_order";s:1:"1";}s:17:"address-field-row";a:1:{s:10:"sort_order";s:1:"2";}s:14:"city-field-row";a:1:{s:10:"sort_order";s:1:"3";}s:23:"phone-company-field-row";a:1:{s:10:"sort_order";s:1:"4";}s:16:"vat_id-field-row";a:1:{s:10:"sort_order";s:1:"5";}}s:10:"attributes";a:18:{s:6:"prefix";a:3:{s:5:"label";s:11:"Name Prefix";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}s:9:"firstname";a:3:{s:5:"label";s:10:"First Name";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}s:10:"middlename";a:3:{s:5:"label";s:19:"Middle Name/Initial";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}s:8:"lastname";a:3:{s:5:"label";s:9:"Last Name";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}s:6:"suffix";a:3:{s:5:"label";s:11:"Name Suffix";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}s:9:"telephone";a:3:{s:5:"label";s:12:"Phone Number";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}s:3:"dni";a:3:{s:5:"label";s:3:"Dni";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}s:6:"street";a:2:{i:0;a:3:{s:5:"label";s:5:"Calle";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}i:1;a:3:{s:5:"label";s:21:"Street Address Line 2";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}}s:13:"street_number";a:3:{s:5:"label";s:7:"Número";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}s:12:"street_floor";a:3:{s:5:"label";s:4:"Piso";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";}s:16:"street_apartment";a:3:{s:5:"label";s:12:"Departamento";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";}s:4:"city";a:3:{s:5:"label";s:4:"City";s:7:"visible";s:1:"1";s:8:"required";s:1:"1";}s:10:"country_id";a:3:{s:5:"label";s:7:"Country";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";}s:6:"region";a:3:{s:5:"label";s:14:"State/Province";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";}s:8:"postcode";a:3:{s:5:"label";s:15:"Zip/Postal Code";s:7:"visible";s:1:"1";s:8:"required";s:1:"0";}s:7:"company";a:3:{s:5:"label";s:7:"Company";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}s:3:"fax";a:3:{s:5:"label";s:3:"Fax";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}s:6:"vat_id";a:3:{s:5:"label";s:10:"VAT Number";s:7:"visible";s:1:"0";s:8:"required";s:1:"0";}}}';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        /* Leyendas compra segura */

        $path = 'aw_osc/trust_seals/enabled';
        $value = '1';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $path = 'aw_osc/trust_seals/label';
        $value = '100% Compra segura';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $path = 'aw_osc/trust_seals/badges';
        $value = 'a:1:{i:0;a:1:{s:6:"script";s:78:"<img src="/media/onestepcheckout/leyendas-seguridad.png" alt="Sitio seguro" />";}}';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID);
    }

    private function addBusinessAttributes(ModuleDataSetupInterface $setup){
        $attributesInfo = [
            'require_invoice' => [
                'label'         => 'Require Invoice',
                'type'          => 'int',
                'input'         => 'boolean',
                "source"        => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'position'      => 130,
                'visible'       => true,
                'system'        => false,
                'user_defined'  => false,
                'required'      => false,
            ],
            'business_name' => [
                'label'         => 'Business Name',
                'type'          => 'text',
                'input'         => 'text',
                'position'      => 131,
                'visible'       => true,
                'system'        => false,
                'user_defined'  => false,
                'required'      => false,
            ],
            'cuit' => [
                'label'         => 'Cuit',
                'type'          => 'text',
                'input'         => 'text',
                'position'      => 132,
                'visible'       => true,
                'system'        => false,
                'user_defined'  => false,
                'required'      => false,
            ]
        ];

        /* customer address attributes columns definitions */

        $columns = [
            'require_invoice' => [
                'type'      => DBDdlTable::TYPE_INTEGER,
                'nullable'  => true,
                'default'   => null,
                'length'    => 11,
                'comment'   => 'Require Invoice',
            ],
            'business_name' => [
                'type'      => DBDdlTable::TYPE_TEXT,
                'nullable'  => true,
                'default'   => null,
                'length'    => 255,
                'comment'   => 'Business Name',
            ],
            'cuit' => [
                'type'      => DBDdlTable::TYPE_TEXT,
                'nullable'  => true,
                'default'   => null,
                'length'    => 255,
                'comment'   => 'Cuit',
            ]
        ];

        /* tables where columns needs to be added */

        $tableNames = [
            'customer_address_entity',
            'quote_address',
            'sales_order_address'
        ];

        /* adding customer address attributes */

        /** @var $customerInstaller CustomerSetup */
        $customerInstaller = $this->customerSetupFactory->create(['setup' => $setup]);
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId(AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS);

        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $customerInstaller->addAttribute('customer_address', $attributeCode, $attributeParams);
            $customerInstaller->getEavConfig()->getAttribute(AddressMetadataInterface::ENTITY_TYPE_ADDRESS, $attributeCode)
                ->setData('attribute_set_id', AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS)
                ->setData('attribute_group_id', $attributeGroupId)
                ->setData('used_in_forms', [
                    'customer_address_edit',
                    'customer_register_address',
                    'adminhtml_customer_address',
                    'customer_address'
                ])
                ->save();
        }

        /* adding customer address attributes columns */

        $connection = $setup->getConnection();
        foreach ($tableNames as $tableName) {
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }

        /* quote and sales attributes columns definitions */

        $columns = [
            'require_invoice' => [
                'type'      => DBDdlTable::TYPE_INTEGER,
                'nullable'  => true,
                'default'   => null,
                'length'    => 11,
                'comment'   => 'Require Invoice',
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

    private function updateAddressTemplateConfigForInvoice(ModuleContextInterface $context){
            $path = 'customer/address_templates/oneline';
            $value = '{{var street}} {{var street_number}} {{var street_floor}} {{var street_apartment}}, CP: {{var postcode}}, {{var city}}, {{var region}} {{if require_invoice}} {{depend business_name}}, Razon Social: {{var business_name}}{{/depend}}, CUIT: {{var cuit}} {{/if}}';

            $this->configInterface->saveConfig(
                $path,
                $value,
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID);

            $path = 'customer/address_templates/text';
            $value = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}
{{depend company}}{{var company}}{{/depend}}
{{if street1}}{{var street1}}
{{var street_number}} {{var street_floor}} {{var street_apartment}}
{{/if}}
{{depend street2}}{{var street2}}{{/depend}}
{{depend street3}}{{var street3}}{{/depend}}
{{depend street4}}{{var street4}}{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}
{{depend telephone}}T: {{var telephone}}{{/depend}}
{{depend fax}}F: {{var fax}}{{/depend}}
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}
{{if require_invoice}}
{{depend business_name}}, Razon Social: {{var business_name}}{{/depend}}, CUIT: {{var cuit}}
{{/if}}';
            $this->configInterface->saveConfig(
                $path,
                $value,
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID);

            $path = 'customer/address_templates/html';
            $value = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}{{depend firstname}}<br />{{/depend}}
{{depend company}}{{var company}}<br />{{/depend}}
{{if street1}}{{var street1}} {{var street_number}} {{var street_floor}} {{var street_apartment}}<br />{{/if}}
{{depend street2}}{{var street2}}<br />{{/depend}}
{{depend street3}}{{var street3}}<br />{{/depend}}
{{depend street4}}{{var street4}}<br />{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}<br />
{{depend telephone}}T: <a href="tel:{{var telephone}}">{{var telephone}}</a>{{/depend}}
{{depend fax}}<br />F: {{var fax}}{{/depend}}
{{depend vat_id}}<br />VAT: {{var vat_id}}{{/depend}}
{{if require_invoice}}
{{depend business_name}}<br />Razon Social: {{var business_name}}{{/depend}} <br/>CUIT: {{var cuit}}{{/if}}';
            $this->configInterface->saveConfig(
                $path,
                $value,
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID);

            $path = 'customer/address_templates/pdf';
            $value = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}|
{{depend company}}{{var company}}|{{/depend}}
{{if street1}}{{var street1}}|{{var street_number}}| {{var street_floor}}| {{var street_apartment}}|{{/if}}
{{depend street2}}{{var street2}}|{{/depend}}
{{depend street3}}{{var street3}}|{{/depend}}
{{depend street4}}{{var street4}}|{{/depend}}
{{if city}}{{var city}}, {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}|
{{depend telephone}}T: {{var telephone}}|{{/depend}}
{{depend fax}}F: {{var fax}}|{{/depend}}|
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}|
{{if require_invoice}}
{{depend business_name}} Razon Social: {{var business_name}}|{{/depend}} CUIT: {{var cuit}}|{{/if}}';
            $this->configInterface->saveConfig(
                $path,
                $value,
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID);


    }

    public function addRequireInvoiceAttributeToGrid(ModuleDataSetupInterface $setup){
        /*change defualt value of require_invoice in sales_order table*/
        $setup->run('UPDATE sales_order SET require_invoice = 0 WHERE require_invoice IS NULL;');
        $setup->run('ALTER TABLE sales_order ALTER COLUMN require_invoice SET DEFAULT 0;');


        $columns = [
            'require_invoice' => [
                'type'      => DBDdlTable::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => 0,
                'length'    => 11,
                'comment'   => 'Require Invoice',
            ]
        ];

        /* tables where columns needs to be added */

        $tableNames = [
            'sales_order_grid'
        ];

        $connection = $setup->getConnection();
        foreach ($tableNames as $tableName) {
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }
    }


    private function addConvenio(ModuleDataSetupInterface $setup){
        $attributesInfo = [
            'convenio' => [
                'label'         => 'Convenio',
                'type'          => 'text',
                'input'         => 'select',
                "source"        => 'Aheadworks\OneStepCheckout\Model\Source\Convenio',
                'position'      => 131,
                'visible'       => true,
                'system'        => false,
                'user_defined'  => false,
                'required'      => false,
            ]
        ];

        /* customer address attributes columns definitions */

        $columns = [
            'convenio' => [
                'type'      => DBDdlTable::TYPE_TEXT,
                'nullable'  => true,
                'default'   => null,
                'length'    => 255,
                'comment'   => 'Convenio',
            ]
        ];

        /* tables where columns needs to be added */

        $tableNames = [
            'customer_address_entity',
            'quote_address',
            'sales_order_address'
        ];

        /* adding customer address attributes */

        /** @var $customerInstaller CustomerSetup */
        $customerInstaller = $this->customerSetupFactory->create(['setup' => $setup]);
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId(AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS);

        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $customerInstaller->addAttribute('customer_address', $attributeCode, $attributeParams);
            $customerInstaller->getEavConfig()->getAttribute(AddressMetadataInterface::ENTITY_TYPE_ADDRESS, $attributeCode)
                ->setData('attribute_set_id', AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS)
                ->setData('attribute_group_id', $attributeGroupId)
                ->setData('used_in_forms', [
                    'customer_address_edit',
                    'customer_register_address',
                    'adminhtml_customer_address',
                    'customer_address'
                ])
                ->save();
        }

        /* adding customer address attributes columns */

        $connection = $setup->getConnection();
        foreach ($tableNames as $tableName) {
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }

        /* quote and sales attributes columns definitions */

        $columns = [
            'convenio' => [
                'type'      => DBDdlTable::TYPE_TEXT,
                'nullable'  => true,
                'default'   => null,
                'length'    => 255,
                'comment'   => 'Convenio',
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

    private function addConvenioRates(ModuleDataSetupInterface $setup){
        $columns = [
            'reg_esp_imp' => [
                'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable' => true,
                'default'  => null,
                'length'   => '12,4',
                'comment'  => 'Importe Reg Esp'
            ],
            'reg_esp_per' => [
                'type'      => DBDdlTable::TYPE_TEXT,
                'nullable'  => true,
                'default'   => null,
                'length'    => 255,
                'comment'   => 'Porcentaje Reg Esp',
            ]
        ];

        /* tables where columns needs to be added */

        $tableNames = [
            'sales_order'
        ];

        $connection = $setup->getConnection();
        foreach ($tableNames as $tableName) {
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }
    }

    private function addAddressDob(ModuleDataSetupInterface $setup){

        /* customer address attributes definition */
        $attributesInfo = [
            'dob'  => [
                'label'         => 'Date of birth',
                'type'          => 'datetime',
                'input'         => 'date',
                'backend'       => 'Magento\Eav\Model\Entity\Attribute\Backend\Datetime',
                'frontend'       => 'Magento\Eav\Model\Entity\Attribute\Frontend\Datetime',
                'position'      => 200,
                'visible'       => true,
                'system'        => false,
                'user_defined'  => false,
                'required'      => false,
            ]
        ];

        /* customer address attributes columns definitions */

        $columns = [
            'dob' => [
                'type'      => DBDdlTable::TYPE_DATE,
                'nullable'  => true,
                'default'   => null,
                'comment'   => 'Date of birth',
            ]
        ];

        /* tables where columns needs to be added */

        $tableNames = [
            'customer_address_entity',
            'quote_address',
            'sales_order_address'
        ];

        /* adding customer address attributes */

        /** @var $customerInstaller CustomerSetup */
        $customerInstaller = $this->customerSetupFactory->create(['setup' => $setup]);
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId(AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS);

        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $customerInstaller->addAttribute('customer_address', $attributeCode, $attributeParams);
            $customerInstaller->getEavConfig()->getAttribute(AddressMetadataInterface::ENTITY_TYPE_ADDRESS, $attributeCode)
                ->setData('attribute_set_id', AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS)
                ->setData('attribute_group_id', $attributeGroupId)
                ->setData('used_in_forms', [
                    'customer_address_edit',
                    'customer_register_address',
                    'adminhtml_customer_address',
                    'customer_address'
                ])
                ->save();
        }

        /* adding customer address attributes columns */

        $connection = $setup->getConnection();
        foreach ($tableNames as $tableName) {
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }
    }


    private function updateAddressTemplateDob(ModuleContextInterface $context){
        $path = 'customer/address_templates/oneline';
        $value = '{{var street}} {{var street_number}} {{var street_floor}} {{var street_apartment}}, CP: {{var postcode}}, {{var city}}, {{var region}} {{if require_invoice}} {{depend business_name}}, Razon Social: {{var business_name}}{{/depend}}, CUIT: {{var cuit}} {{/if}}';

        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $path = 'customer/address_templates/text';
        $value = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}
{{depend dob}}{{var dob}}{{/depend}}
{{depend company}}{{var company}}{{/depend}}
{{if street1}}{{var street1}}
{{var street_number}} {{var street_floor}} {{var street_apartment}}
{{/if}}
{{depend street2}}{{var street2}}{{/depend}}
{{depend street3}}{{var street3}}{{/depend}}
{{depend street4}}{{var street4}}{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}
{{depend telephone}}T: {{var telephone}}{{/depend}}
{{depend fax}}F: {{var fax}}{{/depend}}
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}
{{if require_invoice}}
{{depend business_name}}, Razon Social: {{var business_name}}{{/depend}}, CUIT: {{var cuit}}
{{/if}}';
        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $path = 'customer/address_templates/html';
        $value = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}{{depend firstname}}<br />{{/depend}}
{{depend dob}}{{var dob}}<br />{{/depend}}
{{depend company}}{{var company}}<br />{{/depend}}
{{if street1}}{{var street1}} {{var street_number}} {{var street_floor}} {{var street_apartment}}<br />{{/if}}
{{depend street2}}{{var street2}}<br />{{/depend}}
{{depend street3}}{{var street3}}<br />{{/depend}}
{{depend street4}}{{var street4}}<br />{{/depend}}
{{if city}}{{var city}},  {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}<br />
{{depend telephone}}T: <a href="tel:{{var telephone}}">{{var telephone}}</a>{{/depend}}
{{depend fax}}<br />F: {{var fax}}{{/depend}}
{{depend vat_id}}<br />VAT: {{var vat_id}}{{/depend}}
{{if require_invoice}}
{{depend business_name}}<br />Razon Social: {{var business_name}}{{/depend}} <br/>CUIT: {{var cuit}}{{/if}}';
        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $path = 'customer/address_templates/pdf';
        $value = '{{depend prefix}}{{var prefix}} {{/depend}}{{var firstname}} {{depend middlename}}{{var middlename}} {{/depend}}{{var lastname}}{{depend suffix}} {{var suffix}}{{/depend}}|
{{depend dob}}{{var dob}}|{{/depend}}
{{depend company}}{{var company}}|{{/depend}}
{{if street1}}{{var street1}}|{{var street_number}}| {{var street_floor}}| {{var street_apartment}}|{{/if}}
{{depend street2}}{{var street2}}|{{/depend}}
{{depend street3}}{{var street3}}|{{/depend}}
{{depend street4}}{{var street4}}|{{/depend}}
{{if city}}{{var city}}, {{/if}}{{if region}}{{var region}}, {{/if}}{{if postcode}}{{var postcode}}{{/if}}|
{{depend telephone}}T: {{var telephone}}|{{/depend}}
{{depend fax}}F: {{var fax}}|{{/depend}}|
{{depend vat_id}}VAT: {{var vat_id}}{{/depend}}|
{{if require_invoice}}
{{depend business_name}} Razon Social: {{var business_name}}|{{/depend}} CUIT: {{var cuit}}|{{/if}}';
        $this->configInterface->saveConfig(
            $path,
            $value,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            \Magento\Store\Model\Store::DEFAULT_STORE_ID);


    }
}
