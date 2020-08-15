<?php

namespace Combinatoria\Banner\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class Banner extends Template
{
    private $jsonHelper;
    private $bannerFactory;

    protected $_storeManager;

    public function __construct(
        Context $context,
        \Combinatoria\Banner\Model\BannerFactory $bannerFactory,
        Data $jsonHelper,
        array $data = [],
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder
    ) {
        $this->bannerFactory = $bannerFactory;
        $this->jsonHelper = $jsonHelper;
        $this->_storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }

    public function getBannerCollection()
    {
        $coll = $this->bannerFactory->create()->getCollection();
        $coll->addFieldToFilter('is_active', ['eq'=>1]);
        $coll->addFieldToFilter('is_mobile', ['eq'=>0]);
        $coll->addFieldToFilter('id',['eq'=> $this->getData('id_banner')]);

        return $coll->getData();
    }

    public function getBannerMobileCollection()
    {
        $coll = $this->bannerFactory->create()->getCollection();
        $coll->addFieldToFilter('is_active', ['eq'=>1]);
        $coll->addFieldToFilter('is_mobile', ['eq'=>1]);
        $coll->addFieldToFilter('id',['eq'=> $this->getData('id_banner')]);

        return $coll->getData();
    }



    public function getMediaDirectory()
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }
}