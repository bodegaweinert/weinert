<?php

namespace Combinatoria\ConfiguracionesDefault\Setup;

use Magento\Directory\Helper\Data;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Variable\Model\VariableFactory;

/**
 * Upgrade Data script
 */

class UpgradeData implements UpgradeDataInterface
{
    protected $directoryData;

    private $configInterface;

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    protected $varFActory;

    /**
     * Init
     *
     * @param Data $directoryData
     */
    public function __construct(Data $directoryData,
                                ConfigInterface $configInterface,
                                EavSetupFactory $eavSetupFactory,
                                \Magento\Review\Model\RatingFactory $ratingFactory,
                                \Magento\Framework\ObjectManagerInterface $objectmanager,
                                \Amasty\XmlSitemap\Model\Sitemap $sitemap,
                                VariableFactory $varFactory)
    {
        $this->directoryData = $directoryData;
        $this->configInterface = $configInterface;
        $this->eavSetupFactory              = $eavSetupFactory;
        $this->ratingFactory = $ratingFactory;
        $this->_objectManager = $objectmanager;
        $this->_sitemap = $sitemap;
        $this->varFActory = $varFactory;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(),'1.0.2','<')) {

            $configurations = [
                ["cataloginventory/options/display_product_stock_status","0"],
                ["market/product_detail/show_social_button","0"],
                ["general/store_information/country_id","AR"],
                ["general/country/default","AR"],
                ["general/locale/timezone","America/Argentina/Buenos_Aires"],
                ["general/locale/code","es_AR"],
                ["currency/options/base","ARS"],
                ["currency/options/default","ARS"],
                ["currency/options/allow","ARS"],
                ["market/advanced/show_compare_button","0"]
            ];

            foreach ($configurations as $item){

                $path = $item[0];
                $value = $item[1];
                $this->configInterface->saveConfig(
                    $path,
                    $value,
                    \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    \Magento\Store\Model\Store::DEFAULT_STORE_ID);
            }
        }


        if (version_compare($context->getVersion(),'1.0.3','<')) {
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'volumen',
                [
                    'frontend'  => '',
                    'label'     => 'Volumen',
                    'input'     => 'text',
                    'class'     => '',
                    'global'    => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible'   => true,
                    'required'  => false,
                    'user_defined' => false,
                    'default'   => '',
                    'apply_to'  => '',
                    'visible_on_front'        => false,
                    'is_used_in_grid'         => false,
                    'is_visible_in_grid'      => false,
                    'is_filterable_in_grid'   => false,
                    'used_in_product_listing' => true
                ]
            );

            $eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY,'volumen','apply_to','simple');

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'seo_key',
                [
                    'frontend'  => '',
                    'label'     => 'SEO Key',
                    'input'     => 'text',
                    'class'     => '',
                    'global'    => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible'   => true,
                    'required'  => false,
                    'user_defined' => false,
                    'default'   => '',
                    'apply_to'  => 'simple,configurable,bundle,grouped',
                    'visible_on_front'        => false,
                    'is_used_in_grid'         => false,
                    'is_visible_in_grid'      => false,
                    'is_filterable_in_grid'   => false,
                    'used_in_product_listing' => true
                ]
            );
        }


        if (version_compare($context->getVersion(),'1.0.4','<')) {

            $configurations = [
                ["market/product_detail/show_custom_tab","0"],
                ["sociallogin/google/is_enabled","0"],
                ["sociallogin/twitter/is_enabled","0"],
                ["sociallogin/linkedin/is_enabled","0"],
                ["sociallogin/yahoo/is_enabled","0"],
                ["sociallogin/instagram/is_enabled","0"],
                ["catalog/seo/product_url_suffix",""],
                ["catalog/seo/category_url_suffix",""],
                ["catalog/seo/category_canonical_tag","1"],
                ["catalog/seo/product_canonical_tag","1"],
                ["carriers/tablerate/active","0"],
                ["carriers/flatrate/active","0"],
                ["carriers/flatrate/active","0"],
                ["carriers/freeshipping/active","1"],
                ["carriers/freeshipping/title","A coordinar"],
                ["carriers/freeshipping/name","Envío a acordar con el vendedor"],
                ["general/locale/weight_unit","kgs"]
            ];

            foreach ($configurations as $item){

                $path = $item[0];
                $value = $item[1];
                $this->configInterface->saveConfig(
                    $path,
                    $value,
                    \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    \Magento\Store\Model\Store::DEFAULT_STORE_ID);
            }
        }

        if (version_compare($context->getVersion(),'1.0.5','<')) {
            $eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sm_featured', 'is_user_defined', '0');
        }

        if (version_compare($context->getVersion(),'1.0.6','<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->updateAttribute('catalog_product', 'tax_class_id', 'is_visible', '0');
            $eavSetup->updateAttribute('catalog_product', 'country_of_manufacture', 'is_visible', '0');
        }

        if (version_compare($context->getVersion(),'1.0.7','<')) {

            $configurations = [
                ["catalog/seo/product_use_categories","1"],
                ["amasty_seourl/general/force_redirect","1"],
                ["amasty_seourl/general/product_url_type","default"]
            ];

            foreach ($configurations as $item){

                $path = $item[0];
                $value = $item[1];
                $this->configInterface->saveConfig(
                    $path,
                    $value,
                    \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    \Magento\Store\Model\Store::DEFAULT_STORE_ID);
            }
        }

        if (version_compare($context->getVersion(),'1.0.8','<')) {
            /* Borra los ratings que vienen por default y crea el único que usamos en Dablox que es "Calificación" */
            $ratingCollection = $this->ratingFactory->create()->getResourceCollection();

            $ratingCollection->walk('delete');

            $stores[] = 1;
            $stores[] = 0;
            $ratingModel = $this->_objectManager->create(\Magento\Review\Model\Rating::class);
            $position = 0;
            $isActive = 1;
            $ratingCode = 'Calificación';
            $ratingCodes = [ 1 => 'Calificación'];

            $ratingModel->setRatingCode($ratingCode)
                ->setRatingCodes($ratingCodes)
                ->setStores($stores)
                ->setPosition($position)
                ->setIsActive($isActive)
                ->setEntityId(1)
                ->save();

            $options = ['add_1' => 1,'add_2' => 2,'add_3' => 3,'add_4' => 4,'add_5' => 5];

            if (is_array($options)) {
                $i = 1;
                foreach ($options as $key => $optionCode) {
                    $optionModel = $this->_objectManager->create(\Magento\Review\Model\Rating\Option::class);
                    if (!preg_match("/^add_([0-9]*?)$/", $key)) {
                        $optionModel->setId($key);
                    }

                    $optionModel->setCode($optionCode)
                        ->setValue($i)
                        ->setRatingId($ratingModel->getId())
                        ->setPosition($i)
                        ->save();
                    $i++;
                }
            }
            /* FIN CREACIÓN RATING */

            $configurations = [
                ["cataloginventory/options/display_product_stock_status","1"],
                ["cataloginventory/options/show_out_of_stock","1"],
                ["market/product_detail/show_social_button","1"],
                ["catalog/seo/product_use_categories","0"],
                ["amxmlsitemap/cron/frequency","D"],
                ["amxmlsitemap/cron/time","04,00,00"]
            ];

            foreach ($configurations as $item){

                $path = $item[0];
                $value = $item[1];
                $this->configInterface->saveConfig(
                    $path,
                    $value,
                    \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    \Magento\Store\Model\Store::DEFAULT_STORE_ID);
            }

            /* Atributo SKU visible en ficha de producto */
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY,'sku','is_visible_on_front',1);

            /* Meta Tags By Category */
            $data = json_decode('{"category_id":"1","store_id":"1","product_meta_title":"{name} | {store}","product_meta_description":"Compr\u00e1 {name} en {store}. Abon\u00e1 con tarjeta de cr\u00e9dito o efectivo y disfrut\u00e1 de todas nuestras promociones.","product_meta_keywords":"","product_h1_tag":"","product_short_description":"","product_description":"","cat_meta_title":"{name} en {meta_parent_category}","cat_meta_description":"{name} en {meta_parent_category} de {store}","cat_meta_keywords":"","cat_h1_tag":"","cat_description":"","cat_image_alt":"","cat_after_product_text":"","sub_product_meta_title":"{name} | {store}","sub_product_meta_description":"Compr\u00e1 {name} en {store}. Abon\u00e1 con tarjeta de cr\u00e9dito o efectivo y disfrut\u00e1 de todas nuestras promociones.","sub_product_meta_keywords":"","sub_product_h1_tag":"","sub_product_short_description":"","sub_product_description":""}',true);
            $model = $this->_objectManager->create('Amasty\Meta\Model\\'.'Config');
            $model->addData($data);
            $model->save();

            /* XML Sitemaps */
            $data = json_decode('{"id":"1","title":"Google Sitem Map","store_id":"1","folder_name":"pub\/media\/sitemap.xml","max_items":"0","max_file_size":"0","exclude_urls":"","date_format":"Y-m-d","products":"1","products_thumbs":"0","products_priority":"0.5","products_frequency":"daily","products_modified":"0","exclude_out_of_stock":"0","categories":"1","categories_thumbs":"0","categories_priority":"0.5","categories_frequency":"daily","categories_modified":"0","pages":"1","pages_priority":"0.5","pages_frequency":"daily","pages_modified":"0","exclude_cms_aliases":"","extra":"0","extra_priority":"0.5","extra_frequency":"daily","extra_links":"","landing":"0","landing_priority":"0.5","landing_frequency":"always","blog":"0","blog_priority":"0.5","blog_frequency":"always","brands":"0","brands_priority":"0.5","brands_frequency":"always","navigation":"0","navigation_priority":"0.5","navigation_frequency":"always"}',true);
            $profile = $this->_sitemap;
            $id = 1;
            $profile->setData($data)->setId($id);
            $profile->getResource()->save($profile);

            /* Website Default Robots - va aparte porque es para scope websites */
            $configurations = [
                ["design/search_engine_robots/default_robots","NOINDEX,NOFOLLOW"]
            ];

            foreach ($configurations as $item){

                $path = $item[0];
                $value = $item[1];
                $this->configInterface->saveConfig(
                    $path,
                    $value,
                    'websites',
                    1);
            }
        }


        if (version_compare($context->getVersion(),'1.0.9','<')) {
            $variables = [
                ['code'=>'paso1_neworder_mercadopago_tarjeta','name'=>'Texto del email de nueva compra para mercadopago tarjeta','html_value'=>'1- Estamos esperando que tu pago sea aprobado por el banco emisor de tu tarjeta. Cuando se apruebe, recibirás un 2° e-mail con la confirmación de tu pago.','plain_value'=>'1- Estamos esperando que tu pago sea aprobado por el banco emisor de tu tarjeta. Cuando se apruebe, recibirás un 2° e-mail con la confirmación de tu pago.'],
                ['code'=>'paso1_neworder_mercadopago_ticket','name'=>'Texto del email de nueva compra para mercadopago ticket','html_value'=>'1- Recordá que para confirmar tu compra debes imprimir el ticket y acercarte a una ventanilla de pago. Cuando se acredite recibirás un 2° e-mail con la confirmación de tu pago.','plain_value'=>'1- Recordá que para confirmar tu compra debes imprimir el ticket y acercarte a una ventanilla de pago. Cuando se acredite recibirás un 2° e-mail con la confirmación de tu pago.'],
                ['code'=>'paso1_neworder_banktrasfer','name'=>'Texto del email de nueva compra para transferencia bancaria','html_value'=>'1- Recordá que para confirmar tu compra debes realizar la transferencia bancaria. Cuando se acredite recibirás un 2° e-mail con la confirmación de tu pago.','plain_value'=>'1- Recordá que para confirmar tu compra debes realizar la transferencia bancaria. Cuando se acredite recibirás un 2° e-mail con la confirmación de tu pago.'],
                ['code'=>'paso1_neworder_storepayment','name'=>'Texto del email de nueva compra para pago en sucursal','html_value'=>'1- Para confirmar tu compra deberás realizar el pago al momento del retiro.','plain_value'=>'1- Para confirmar tu compra deberás realizar el pago al momento del retiro.'],
                ['code'=>'paso1_neworder_default','name'=>'Texto del email de nueva compra para otros método de pago','html_value'=>'','plain_value'=>''],
                ['code'=>'paso1_accredited','name'=>'Texto del email de pago acreditado','html_value'=>'El pago de tu pedido fue aprobado con éxito, por lo que continuaremos con el proceso y su posterior envío. Cuando lo despachemos te enviaremos un nuevo email.','plain_value'=>'El pago de tu pedido fue aprobado con éxito, por lo que continuaremos con el proceso y su posterior envío. Cuando lo despachemos te enviaremos un nuevo email.'],
                ['code'=>'paso1_delivered','name'=>'Texto del email de despachado','html_value'=>'Te informamos que tu pedido ya fue despachado y va a llegar a la dirección de entrega que especificaste.','plain_value'=>'Te informamos que tu pedido ya fue despachado y va a llegar a la dirección de entrega que especificaste.'],
                ['code'=>'paso2_storepickup','name'=>'Texto del email de nueva compra para retiro en sucursal','html_value'=>'2- Para efectuar el retiro de tu compra deberás acercarte al local que elegiste.','plain_value'=>'2- Para efectuar el retiro de tu compra deberás acercarte al local que elegiste.'],
                ['code'=>'paso2_ownlogisticsservice','name'=>'Texto del email de nueva compra para logística propia','html_value'=>'2- Recibirás un 3° e-mail avisándote cuando despachemos tu pedido.','plain_value'=>'2- Recibirás un 3° e-mail avisándote cuando despachemos tu pedido.'],
                ['code'=>'paso2_andreani','name'=>'Texto del email de nueva compra para Andreani','html_value'=>'2- Recibirás un 3° e-mail avisándote cuando despachemos tu pedido.','plain_value'=>'2- Recibirás un 3° e-mail avisándote cuando despachemos tu pedido.'],
                ['code'=>'paso2_default','name'=>'Texto del email de nueva compra para otros métodos de envío','html_value'=>'2- Nos contactaremos a la brevedad para coordinar el envio','plain_value'=>'2- Nos contactaremos a la brevedad para coordinar el envio']
            ];

            foreach ($variables as $data){
                $variable = $this->varFActory->create();
                $variable->setData($data);
                $variable->save();
            }
        }

        $setup->endSetup();
    }
}