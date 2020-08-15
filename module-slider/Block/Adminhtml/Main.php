<?php
namespace Combinatoria\Slider\Block\Adminhtml;
use Magento\Backend\Block\Template\Context;
use Combinatoria\Slider\Model\SliderFactory;
use Magento\Backend\Block\Template;

class Main extends Template
{
    /**
     * @var \Combinatoria\Slider\Model\SliderFactory
     */
    private $sliderFactory;

    function _prepareLayout() {}

    /**
     * @param \Combinatoria\Slider\Model\SliderFactory $sliderFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = [],
        SliderFactory $sliderFactory
    ) {
        parent::__construct($context, $data);
        $this->sliderFactory = $sliderFactory;
    }

}