<?php

namespace Combinatoria\CustomRoles\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/* For get RoleType and UserType for create Role   */;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * RoleFactory
     *
     * @var roleFactory
     */
    private $roleFactory;

    /**
     * RulesFactory
     *
     * @var rulesFactory
     */
    private $rulesFactory;
    /**
     * Init
     *
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param \Magento\Authorization\Model\RulesFactory $rulesFactory
     */

    /**
     * User model factory
     *
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    public function __construct(
        \Magento\Authorization\Model\RoleFactory $roleFactory, /* Instance of Role*/
        \Magento\Authorization\Model\RulesFactory $rulesFactory, /* Instance of Rule */
        \Magento\User\Model\UserFactory $userFactory
    )
    {
        $this->roleFactory = $roleFactory;
        $this->rulesFactory = $rulesFactory;
        $this->_userFactory = $userFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Create Warehouse role
         */
        $role=$this->roleFactory->create();
        $role->setName('Owner') //Set Role Name Which you want to create
        ->setPid(0) //set parent role id of your role
        ->setRoleType(RoleGroup::ROLE_TYPE)
            ->setUserType(UserContextInterface::USER_TYPE_ADMIN);
        $role->save();
        /* Now we set that which resources we allow to this role */
        $resource=['Magento_Backend::admin',
            'Magento_Backend::dashboard',
            'Magento_Sales::sales',
            'Magento_Sales::sales_operation',
            'Magento_Sales::sales_order',
            'Magento_Sales::actions',
            'Magento_Sales::create',
            'Magento_Sales::actions_view',
            'Magento_Sales::email',
            'Magento_Sales::reorder',
            'Magento_Sales::actions_edit',
            'Magento_Sales::cancel',
            'Magento_Sales::review_payment',
            'Magento_Sales::capture',
            'Magento_Sales::invoice',
            'Magento_Sales::creditmemo',
            'Magento_Sales::hold',
            'Magento_Sales::unhold',
            'Magento_Sales::ship',
            'Magento_Sales::comment',
            'Magento_Sales::emails',
            'Magento_Sales::shipment',
            'Magento_Catalog::catalog',
            'Magento_Catalog::catalog_inventory',
            'Magento_Catalog::products',
            'Magento_Catalog::categories',
            'Amasty_Label::label',
            'Magento_Customer::customer',
            'Magento_Customer::manage',
            'Magento_Backend::stores',
            'Magento_Backend::stores_other_settings',
            'Magento_Customer::group',
            'Magento_Backend::marketing',
            'Magento_CatalogRule::promo',
            'Magento_CatalogRule::promo_catalog',
            'Magento_SalesRule::quote',
            'Magento_Backend::marketing_communications',
            'Magento_Newsletter::subscriber',
            'Magento_Backend::marketing_user_content',
            'Magento_Review::reviews_all',
            'Magento_Backend::stores',
            'Ves_Megamenu::menu',
            'Magento_Backend::menu_elements',
            'Ves_Megamenu::config_megamenu',
            'Ves_Megamenu::configuration',
            'Ves_Megamenu::menu_edit',
            'Ves_Megamenu::menu_save',
            'Ves_Megamenu::menu_delete',
            'Magento_Backend::metodos_envio_menu',
            'Combinatoria_OcaOl::combinatoria_ocaol_operatorias_section',
            'Combinatoria_OcaOl::combinatoria_ocaol_operatorias',
            'Combinatoria_Operatoria::save',
            'Combinatoria_Operatoria::delete',
            'Combinatoria_OwnLogisticsService::combinatoria_ownlogisticsservice_section',
            'Combinatoria_OwnLogisticsService::combinatoria_ownlogisticsservice_shipments',
            'Combinatoria_OwnLogisticsService::combinatoria_ownlogisticsservice_regions',
            'Combinatoria_OwnLogisticsService::delete',
            'Combinatoria_OwnLogisticsService::save',
            'Combinatoria_StorePickup::combinatoria_storepickup_section ',
            'Combinatoria_StorePickup::combinatoria_storepickup_branches',
            'Combinatoria_StorePickup::save',
            'Combinatoria_StorePickup::delete',
            'Combinatoria_Slider::combinatoria_slider_section',
            'Combinatoria_Slider::combinatoria_slider_sliders',
            'Combinatoria_Slider::save',
            'Combinatoria_Slider::delete',
            'Combinatoria_Banner::combinatoria_banner_section',
            'Combinatoria_Banner::combinatoria_banner_banners',
            'Combinatoria_Banner::save',
            'Magento_Backend::pagebuilder_elements',
            'Ves_PageBuilder::block',
            'Ves_PageBuilder::block_new',
            'Ves_PageBuilder::block_edit',
            'Ves_PageBuilder::block_save',
            'Ves_PageBuilder::block_delete',
            'Ves_PageBuilder::block_convert_template',
            'Ves_PageBuilder::page',
            'Ves_PageBuilder::page_new',
            'Ves_PageBuilder::page_edit',
            'Ves_PageBuilder::page_save',
            'Ves_PageBuilder::page_delete',
            'Ves_PageBuilder::page_convert_template',
            'Ves_PageBuilder::config',
            'Ves_PageBuilder::config_pagebuilder',
            'Ves_PageBuilder::config_livecss',
            'Magento_Widget::widget_instance',
            'Magento_Backend::system',
            'Magento_Backend::tools',
            'Magento_Backend::cache',
            'Magento_AdminNotification::adminnotification',
            'Magento_AdminNotification::show_toolbar',
            'Magento_AdminNotification::show_list',
            'Magento_AdminNotification::mark_as_read',
            'Magento_AdminNotification::adminnotification_remove'

        ];
        /* Array of resource ids which we want to allow this role*/
        $this->rulesFactory->create()->setRoleId($role->getId())->setResources($resource)->saveRel();


        $adminInfo = [
            'username'  => 'owner',
            'firstname' => 'owner',
            'lastname'    => 'owner',
            'email'     => 'owner@combinatoria.com.ar',
            'password'  =>'owner_1234',
            'interface_locale' => 'es_AR',
            'is_active' => 1
        ];

        $userModel = $this->_userFactory->create();
        $userModel->setData($adminInfo);
        $userModel->setRoleId($role->getId());
        try{
            $userModel->save();
        } catch (\Exception $ex) {
            $ex->getMessage();
        }
    }
}