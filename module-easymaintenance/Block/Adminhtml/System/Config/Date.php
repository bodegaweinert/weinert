<?php

namespace Biztech\Easymaintenance\Block\Adminhtml\System\Config;

use Magento\Framework\Registry;

class Date extends \Magento\Config\Block\System\Config\Form\Field {

    /**
     * @var  Registry
     */
    protected $_coreRegistry;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, Registry $coreRegistry, array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $baseURL = $this->getBaseUrl();
        $html = $element->getElementHtml();

        $calpath = $baseURL . 'pub/media/easymaintenance/';

        if (!$this->_coreRegistry->registry('datepicker_loaded')) {
            $html .= '<style type="text/css">input.datepicker { background-image: url(' . $calpath . 'calendar.png) !important; background-position: calc(100% - 8px) center; background-repeat: no-repeat; } input.datepicker.disabled,input.datepicker[disabled] { pointer-events: none; }</style>';
            $this->_coreRegistry->registry('datepicker_loaded', 1);
        }
        $html .= '<script type="text/javascript">
            require(["jquery", "jquery/ui"], function () {
                jQuery(document).ready(function () {
                    jQuery("#' . $element->getHtmlId() . '").datepicker( { dateFormat: "mm/dd/yy" } );
                        
                    var el = document.getElementById("' . $element->getHtmlId() . '");
                    el.className = el.className + " datepicker";
                });
            });
            </script>';
        return $html;

        // return parent::_getElementHtml($element);
    }

}
