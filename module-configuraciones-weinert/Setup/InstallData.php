<?php

namespace Combinatoria\ConfiguracionesWeinert\Setup;

use Magento\Directory\Helper\Data;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;


class InstallData implements InstallDataInterface
{

    /**
     * Directory data
     *
     * @var Data
     */
    protected $directoryData;

    private $configInterface;

    protected $_objectManager = null;
    /**
     * Init
     *
     * @param Data $directoryData
     */
    public function __construct(Data $directoryData,ConfigInterface $configInterface,\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->directoryData = $directoryData;
        $this->configInterface = $configInterface;
        $this->_objectManager = $objectManager;
    }


    /**
     * Install Data
     *
     * @param ModuleDataSetupInterface $setup   Module Data Setup
     * @param ModuleContextInterface   $context Module Context
     *
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $configurations = [
            ["checkout/cart/configurable_product_image","itself"],
            ['market/advanced/compile_less',
                '1'],

            ['market/advanced/copyright_content',
                '&copy; Weinert 2018 | Prohibida la venta de alcohol a menores de 18 años'],

            ['market/advanced/custom_copyright',
                '1'],

            ['market/advanced/custom_css',
                NULL],

            ['market/advanced/custom_js',
                NULL],

            ['market/advanced/enable_ladyloading',
                '1'],

            ['market/advanced/show_addtocart_button',
                '1'],

            ['market/advanced/show_compare_button',
                '0'],

            ['market/advanced/show_gototop',
                '1'],

            ['market/advanced/show_loadingpage',
                '0'],

            ['market/advanced/show_newlabel',
                '1'],

            ['market/advanced/show_newsletter_popup',
                '0'],

            ['market/advanced/show_salelabel',
                '1'],

            ['market/advanced/show_wishlist_button',
                '1'],

            ['market/general/background_customize_image',
                NULL],

            ['market/general/background_position',
                'left top'],

            ['market/general/background_repeat',
                'repeat-x'],

            ['market/general/body_background_color',
                'FFFFFF'],

            ['market/general/body_background_image',
                'pattern2'],

            ['market/general/body_font',
                'google_font'],

            ['market/general/body_google_font',
                'Muli'],

            ['market/general/body_link_color',
                '922139'],

            ['market/general/body_link_hover_color',
                'E19612'],

            ['market/general/body_text_color',
                '9A9999'],

            ['market/general/element_google_font',
                'Aclonica'],

            ['market/general/font_size',
                '14px'],

            ['market/general/google_font_targets',
                NULL],

            ['market/general/home_page_mobile',
                'home-mobile'],

            ['market/general/menu_ontop',
                '1'],

            ['market/general/menu_style',
                'css'],

            ['market/general/mobile_layout',
                '0'],

            ['market/general/responsive_menu',
                'sidebar'],

            ['market/general/theme_color',
                '9A865B'],

            ['market/general/use_background_image',
                '0'],

            ['market/general/use_customize_image',
                '0'],

            ['market/product_detail/breakpoints_width',
                '1200'],

            ['market/product_detail/customtab_content',
                '<table class="data-table" style="width: 100%;" border="1">\r\n<tbody>\r\n<tr>\r\n<td>Brand</td>\r\n<td>Description</td>\r\n</tr>\r\n<tr>\r\n<td>History</td>\r\n<td>Color sit amet, consectetur adipiscing elit. In gravida pellentesque ligula, vel eleifend turpis blandit vel. Nam quis lorem ut mi mattis ullamcorper ac quis dui. Vestibulum et scelerisque ante, eu sodales mi. Nunc tincidunt tempus varius. Integer ante dolor, suscipit non faucibus a, scelerisque vitae sapien.</td>\r\n</tr>\r\n</tbody>\r\n</table>'],

            ['market/product_detail/custom_tab_name',
                'Custom Tab'],

            ['market/product_detail/detail_style',
                'detail-1'],

            ['market/product_detail/image_fullscreen',
                '1'],

            ['market/product_detail/image_keyboard',
                '1'],

            ['market/product_detail/image_loop',
                '1'],

            ['market/product_detail/image_navigation',
                '1'],

            ['market/product_detail/lightbox_types',
                'button'],

            ['market/product_detail/mouse_lightbox',
                '1'],

            ['market/product_detail/mouse_zoom',
                '0'],

            ['market/product_detail/next_prev_effect',
                'none'],

            ['market/product_detail/open_close_effect',
                'none'],

            ['market/product_detail/related_limit',
                '6'],

            ['market/product_detail/show_custom_tab',
                '1'],

            ['market/product_detail/show_related',
                '1'],

            ['market/product_detail/show_social_button',
                '1'],

            ['market/product_detail/show_upsell',
                '1'],

            ['market/product_detail/tab_style',
                'vertical'],

            ['market/product_detail/thumbs_borderwidth',
                '1'],

            ['market/product_detail/thumbs_height',
                '132'],

            ['market/product_detail/thumbs_margin',
                '11'],

            ['market/product_detail/thumbs_navigation',
                '1'],

            ['market/product_detail/thumbs_style',
                'horizontal'],

            ['market/product_detail/thumbs_width',
                '132'],

            ['market/product_detail/upsell_limit',
                '6'],

            ['market/product_detail/use_zoom_image',
                '1'],

            ['market/product_detail/zoom_mode',
                'inner'],

            ['market/product_detail/zoom_width',
                '456'],

            ['market/product_listing/onecolumn_device_1200',
                '4'],

            ['market/product_listing/onecolumn_device_481',
                '2'],

            ['market/product_listing/onecolumn_device_768',
                '3'],

            ['market/product_listing/onecolumn_device_992',
                '4'],

            ['market/product_listing/onecolumn_device_less_481',
                '1'],

            ['market/product_listing/threecolumn_device_1200',
                '2'],

            ['market/product_listing/threecolumn_device_481',
                '1'],

            ['market/product_listing/threecolumn_device_768',
                '1'],

            ['market/product_listing/threecolumn_device_992',
                '1'],

            ['market/product_listing/threecolumn_device_less_481',
                '1'],

            ['market/product_listing/twocolumn_device_1200',
                '3'],

            ['market/product_listing/twocolumn_device_481',
                '2'],

            ['market/product_listing/twocolumn_device_768',
                '3'],

            ['market/product_listing/twocolumn_device_992',
                '3'],

            ['market/product_listing/twocolumn_device_less_481',
                '1'],

            ['market/socials/facebook_name',
                'Facebook'],

            ['market/socials/facebook_url',
                'http://www.facebook.com/'],

            ['market/socials/google_name',
                'Google+'],

            ['market/socials/google_url',
                '#'],

            ['market/socials/instagram_name',
                'Instagram'],

            ['market/socials/instagram_url',
                '#'],

            ['market/socials/linkedin_name',
                'Linkedin'],

            ['market/socials/linkedin_url',
                '#'],

            ['market/socials/pinterest_name',
                'Pinterest'],

            ['market/socials/pinterest_url',
                'Linkedin'],

            ['market/socials/show_facebook',
                '1'],

            ['market/socials/show_google',
                '0'],

            ['market/socials/show_instagram',
                '1'],

            ['market/socials/show_linkedin',
                '0'],

            ['market/socials/show_pinterest',
                '0'],

            ['market/socials/show_twitter',
                '1'],

            ['market/socials/show_youtube',
                '0'],

            ['market/socials/twitter_name',
                'Twitter'],

            ['market/socials/twitter_url',
                'https://twitter.com/'],

            ['market/socials/youtube_name',
                'Youtube'],

            ['market/socials/youtube_url',
                '#'],

            ['market/theme_install/overwrite_blocks',
                '1'],

            ['market/theme_install/overwrite_pages',
                '0'],

            ['market/theme_layout/device_responsive',
                '1'],

            ['market/theme_layout/direction_rtl',
                '0'],

            ['market/theme_layout/footer_style',
                'footer-4'],

            ['market/theme_layout/header_style',
                'header-9'],

            ['market/theme_layout/home_style',
                'home-9'],

            ['market/theme_layout/layout_style',
                'full_width'],

            ['market/theme_layout/max_width',
                '1170'],

            ['searchbox/general/isenabled',
                '1'],

            ['searchbox/general/root_catalog',
                '2'],

            ['searchbox/general/max_level',
                '3'],

            ['searchbox/general/show_popular',
                '0'],

            ['searchbox/general/limit_popular',
                '5'],

            ['searchbox/general/show_more',
                '0'],

            ['searchbox/general/more_text',
                NULL],

            ['searchbox/general/show_advanced',
                '0'],

            ['searchbox/general/pretext',
                NULL],

            ['searchbox/general/posttext',
                NULL],

            ['trans_email/ident_general/name',
                'Weinert'],

            ['trans_email/ident_sales/name',
                'Weinert'],

            ['trans_email/ident_support/name',
                'Weinert'],

            ['web/default/cms_home_page',
                'home-demo-09'],

            ['web/default/cms_no_route',
                'no-route']

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

        //SLIDER

        $slide =
        [
            "alt" => "Weinert",
            "link" => "http://dablox.com",
            "from" => "2018-08-01",
            "to" => "2034-08-01",
            "image" => "images/slider.jpg",
            "sort_order" => "1",
            "is_mobile" => "0",
            "is_active" => "1"
        ];

        $sliderModel = $this->_objectManager->create('\Combinatoria\Slider\Model\Slider');
        $sliderModel->setData( $slide );
        $sliderModel->save();
    }

}

