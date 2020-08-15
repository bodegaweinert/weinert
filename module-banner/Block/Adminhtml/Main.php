<?php
namespace Combinatoria\Banner\Block\Adminhtml;
use Magento\Backend\Block\Template\Context;
use Combinatoria\Banner\Model\BannerFactory;
use Magento\Backend\Block\Template;

class Main extends Template
{
    /**
     * @var \Combinatoria\Banner\Model\BannerFactory
     */
    private $bannerFactory;

    function _prepareLayout() {}

    /**
     * @param \Combinatoria\Banner\Model\BannerFactory $bannerFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = [],
        BannerFactory $bannerFactory
    ) {
        parent::__construct($context, $data);
        $this->bannerFactory = $bannerFactory;
    }

}