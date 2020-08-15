<?php

namespace Combinatoria\Slider\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class Slider extends Template
{
    private $jsonHelper;
    private $sliderFactory;

    protected $_storeManager;

    public function __construct(
        Context $context,
        \Combinatoria\Slider\Model\SliderFactory $sliderFactory,
        Data $jsonHelper,
        array $data = [],
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder
    ) {
        $this->sliderFactory = $sliderFactory;
        $this->jsonHelper = $jsonHelper;
        $this->_storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }

    public function getSliderCollection()
    {
        $coll = $this->sliderFactory->create()->getCollection();
        $coll->setOrder('sort_order', 'ASC');
        $coll->addFieldToFilter('is_active', ['eq'=>1]);
        $coll->addFieldToFilter('is_mobile', ['eq'=>0]);
        $coll->addFieldToFilter('from',['lteq'=> date('Y-m-d')]);
        $coll->addFieldToFilter('to',['gteq'=> date('Y-m-d')]);

        return $coll->getData();
    }

    public function getSliderMobileCollection()
    {
        $coll = $this->sliderFactory->create()->getCollection();
        $coll->setOrder('sort_order', 'ASC');
        $coll->addFieldToFilter('is_active', ['eq'=>1]);
        $coll->addFieldToFilter('is_mobile', ['eq'=>1]);
        $coll->addFieldToFilter('from',['lteq'=> date('Y-m-d')]);
        $coll->addFieldToFilter('to',['gteq'=> date('Y-m-d')]);

        return $coll->getData();
    }

    public function getMediaDirectory()
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }
}