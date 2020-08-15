<?php

/**
 * Color Status
 *
 * Set the each sales row with a background color on sales grid.
 *
 * @package Igorludgero\Colorstatus
 * @author Igor Ludgero Miura <igor@imaginemage.com>
 * @copyright Copyright (c) 2017 Igor Ludgero Miura (https://www.igorludgero.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

namespace Igorludgero\Colorstatus\Block\Adminhtml\Field;

class Color extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    /**
     * Return the custom input html for color field.
     * @return string
     */
   public function getElementHtml(){
        $htmlElement = '<input id="color" name="color" data-ui-id="sales-order-status-edit-container-form-fieldset-element-text-color" class="input-text admin__control-text" type="color" value="'.$this->getData('value').'" >';
        return $htmlElement;
    }

}